# Test report

Executed in a Linux container with Node.js 22.16.0. Environment is not the user's Windows/WSL installation.

| Verification | Result |
|---|---|
| `npm test` | PASS — 13 tests |
| `node --check src/models.js` | PASS |
| `node --check src/server.js` | PASS |
| Actual GUI screenshots / manual interactive click | **Not tested** |
| User's WSL2 Ubuntu environment | **Not tested** |
| Recursion automated grader | **Not tested** |
| GitHub push | **Not performed** |

## Test execution transcript

```text

> restaurant-chain-mockup@1.0.0 test
> node --test

TAP version 13
# Subtest: User implements conversion contract
ok 1 - User implements conversion contract
  ---
  duration_ms: 2.451077
  type: 'test'
  ...
# Subtest: Employee implements conversion contract
ok 2 - Employee implements conversion contract
  ---
  duration_ms: 12.471383
  type: 'test'
  ...
# Subtest: Company implements conversion contract
ok 3 - Company implements conversion contract
  ---
  duration_ms: 0.288455
  type: 'test'
  ...
# Subtest: RestaurantLocation implements conversion contract
ok 4 - RestaurantLocation implements conversion contract
  ---
  duration_ms: 0.343759
  type: 'test'
  ...
# Subtest: RestaurantChain implements conversion contract
ok 5 - RestaurantChain implements conversion contract
  ---
  duration_ms: 0.617752
  type: 'test'
  ...
# Subtest: Employee extends User
ok 6 - Employee extends User
  ---
  duration_ms: 0.099701
  type: 'test'
  ...
# Subtest: RestaurantChain extends Company
ok 7 - RestaurantChain extends Company
  ---
  duration_ms: 0.316227
  type: 'test'
  ...
# Subtest: chain location counter is consistent
ok 8 - chain location counter is consistent
  ---
  duration_ms: 0.144919
  type: 'test'
  ...
# Subtest: location mismatch rejected
ok 9 - location mismatch rejected
  ---
  duration_ms: 0.432312
  type: 'test'
  ...
# Subtest: generated branch employees are Employee objects
ok 10 - generated branch employees are Employee objects
  ---
  duration_ms: 0.600846
  type: 'test'
  ...
# Subtest: HTML is escaped
ok 11 - HTML is escaped
  ---
  duration_ms: 0.259812
  type: 'test'
  ...
# Subtest: chain HTML contains every location
ok 12 - chain HTML contains every location
  ---
  duration_ms: 0.162806
  type: 'test'
  ...
# Subtest: server serves UI and returns error for unknown route
ok 13 - server serves UI and returns error for unknown route
  ---
  duration_ms: 34.79193
  type: 'test'
  ...
1..13
# tests 13
# suites 0
# pass 13
# fail 0
# cancelled 0
# skipped 0
# todo 0
# duration_ms 120.35583

```

## Cases covered

Conversion methods for all 5 concrete classes; Employee/User and RestaurantChain/Company inheritance; count consistency; mismatched count rejection; nested employee instance type; HTML encoding; all locations included in HTML; live HTTP GET responses including 200 and 404.

## Known limitations

- Visual similarity to example has not been confirmed with an actual rendered-browser screenshot.
- HTML `details` works by browser standards, but was not manually clicked in this execution environment.
- Plain JavaScript does not implement a formal type-checked `interface`.
