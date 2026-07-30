# Contributing

Thank you for contributing to Rave Custom Shipping.

## Before opening a pull request

1. Fork the repository and create a focused branch.
2. Keep changes minimal and avoid duplicating existing functions.
3. Sanitize all input and escape all output at the correct context.
4. Use WooCommerce CRUD APIs for order data and preserve HPOS compatibility.
5. Do not add direct SQL unless it is necessary, targeted, paginated where applicable, and supported by appropriate indexes.
6. Do not include credentials, customer information, order data, or other secrets.
7. Run PHP syntax checks:

   ```powershell
   Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
   ```

8. Update `README.md`, `readme.txt`, and the changelog when behavior changes.

## Pull requests

Explain:

- What changed.
- Why the change is needed.
- What user-facing behavior is affected.
- Which checks were run.

Keep unrelated changes in separate pull requests.

## Security reports

Do not report vulnerabilities through public issues or pull requests. Follow [SECURITY.md](SECURITY.md).
