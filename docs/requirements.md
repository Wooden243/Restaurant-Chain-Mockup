# Requirements and implementation traceability

Source: Recursion “Restaurant Chain Mockup” screenshot supplied in this conversation. Distinguish explicit requirements from reasonable implementations of omitted details.

| ID | Requirement | Implemented in | Verification | Status |
|---|---|---|---|---|
| R1 | Employee extends User; role, salary, start date, awards | `src/models.js`: `Employee` | inheritance and conversion tests | Pass |
| R2 | Company company name, year, description, website, phone, industry, CEO, public flag | `Company` | conversions, page rendering | Pass |
| R3 | RestaurantChain extends Company, chain ID, locations, cuisine, count, drive-through, founded year, parent | `RestaurantChain` | inheritance and count tests | Pass |
| R4 | RestaurantLocation shows address, city, state, postcode and employees | `RestaurantLocation` | rendering tests | Pass |
| R5 | FileConvertible interface with three conversions for all classes | `FileConvertible` superclass + overrides | 5 conversion tests | Pass; JS interface emulation |
| R6 | Display methods for all classes | model `toString`, `toHTML`, `toMarkdown` | 5 conversion tests | Pass |
| R7 | RestaurantChain can add/retrieve location information | `restaurantLocations` holds locations, rendered by `toHTML` | HTML includes each location | Pass via constructor and read access; no addLocation method specified |
| R8 | Generate random mock records using randomGenerator | static `randomGenerator()` in all 5 concrete classes | conversion and structure tests | Pass |
| R9 | Render random chain details and staff on page load | `src/server.js` HTTP handler | HTTP test | Pass |
| R10 | Show expandable accordion-style locations | HTML `details` and `summary` | HTML structural test | Pass; browser visual unverified |
| R11 | Run with JavaScript (Node.js) | `package.json`, `src/server.js` | HTTP test | Pass |
| R12 | README, tests, learning guides, walkthrough | `README.md`, `tests/`, `learning/` | ZIP integrity | Pass |

## Explicit versus assumed details

**Explicit:** class hierarchy, company/chain/employee attributes, `FileConvertible` method names, conversion/display methods, random generation, and randomly generated website rendering with locations and staff.

**Assumed:** exact `User` structure (id/name/email), exact `RestaurantLocation` object property spellings beyond name/address/city/state/zip, all literal mock values, sample record count (three chains), salary currency, and HTML styling. Image does not provide an exhaustive API signature or starter repository. These are not represented as verified literal requirements.

## Environment and limits

- Built using Node.js core only; server-side rendering, no PHP needed for this JavaScript-tagged exercise.
- Interface concept implemented using a shared base class because native JavaScript lacks an `interface` keyword. Methods throw when subclass does not implement them.
- `yearFounded` belongs to the parent Company and is inherited by RestaurantChain, avoiding two independent years.
- `numberOfLocations` is validated against array length at construction. Direct mutation of public array after construction may cause drift; production code should encapsulate mutators.
- `isPublic` here means stock-market listing, not restaurant website visibility.
- No database or persistent storage is required by the screenshot.
- Values are generated using `Math.random()`; they are not deterministic or cryptographically secure.
- Generated data represents fictional restaurants and staff; it is not fetched from real services.
