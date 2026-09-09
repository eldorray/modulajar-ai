#!/usr/bin/env python3
"""Validate the API v1 contract without third-party dependencies."""
from __future__ import annotations
import json, sys
from pathlib import Path
from typing import Any, Iterator
SPEC_PATH = Path(__file__).resolve().parents[1] / "openapi" / "api-v1.yaml"
HTTP_METHODS = {"get", "post", "put", "patch", "delete", "options", "head", "trace"}
PUBLIC_OPERATIONS = {"login"}
def walk(value: Any) -> Iterator[Any]:
    yield value
    if isinstance(value, dict):
        for child in value.values(): yield from walk(child)
    elif isinstance(value, list):
        for child in value: yield from walk(child)
def resolve(document: Any, reference: str) -> None:
    if not reference.startswith("#/"): raise ValueError(f"Only local references are allowed: {reference}")
    current = document
    for token in reference[2:].split("/"):
        token = token.replace("~1", "/").replace("~0", "~")
        if not isinstance(current, dict) or token not in current: raise ValueError(f"Unresolved reference: {reference}")
        current = current[token]
def main() -> int:
    errors: list[str] = []
    try: document = json.loads(SPEC_PATH.read_text(encoding="utf-8"))
    except (OSError, json.JSONDecodeError) as error:
        print(f"FAIL: cannot parse {SPEC_PATH}: {error}", file=sys.stderr); return 1
    if document.get("openapi") != "3.1.0": errors.append("openapi must be 3.1.0")
    if not document.get("servers", [{}])[0].get("url", "").endswith("/api/v1"): errors.append("first server URL must end with /api/v1")
    operation_ids: dict[str, str] = {}; operation_count = 0
    for path, item in document.get("paths", {}).items():
        for method, operation in item.items():
            if method not in HTTP_METHODS: continue
            operation_count += 1; location = f"{method.upper()} {path}"; oid = operation.get("operationId")
            if not oid: errors.append(f"missing operationId: {location}")
            elif oid in operation_ids: errors.append(f"duplicate operationId {oid}: {operation_ids[oid]} and {location}")
            else: operation_ids[oid] = location
            if oid not in PUBLIC_OPERATIONS and not operation.get("security", document.get("security")): errors.append(f"protected operation has no security: {location}")
            if "responses" not in operation: errors.append(f"missing responses: {location}")
    for node in walk(document):
        if isinstance(node, dict) and isinstance(node.get("$ref"), str):
            try: resolve(document, node["$ref"])
            except ValueError as error: errors.append(str(error))
    idem = document.get("components", {}).get("parameters", {}).get("IdempotencyKey", {})
    if idem.get("name") != "Idempotency-Key" or not idem.get("required"): errors.append("Idempotency-Key must be a required reusable header")
    required = {"SuccessEnvelope", "Error", "User", "Rpp", "Sts", "LjkResult"}; schemas = set(document.get("components", {}).get("schemas", {}))
    if required - schemas: errors.append("missing required schemas: " + ", ".join(sorted(required - schemas)))
    if errors:
        print(f"FAIL: {len(errors)} contract issue(s)", file=sys.stderr)
        for error in errors: print(f"- {error}", file=sys.stderr)
        return 1
    refs = sum(1 for node in walk(document) if isinstance(node, dict) and isinstance(node.get("$ref"), str))
    print(f"PASS: OpenAPI {document['openapi']}; {len(document['paths'])} paths; {operation_count} operations; {len(operation_ids)} unique operationIds; {refs} resolved local references")
    return 0
if __name__ == "__main__": raise SystemExit(main())
