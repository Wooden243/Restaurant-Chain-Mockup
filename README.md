# Restaurant Chain Mockup

A runnable Node.js restaurant chain demonstration using object-oriented programming, generated sample records, and expandable HTML profiles.

## Features

- `FileConvertible` contract: `toString()`, `toHTML()`, `toMarkdown()`
- `Employee extends User`, `RestaurantChain extends Company`
- Randomly generated companies, restaurant branches, and employees
- HTML UI with collapsible `<details>` sections
- HTML escaping for mock-data display
- Text and Markdown export endpoints
- Automated Node.js tests

## Requirements

Node.js **18 or later**. No npm dependencies or credentials are needed. Intended for Windows + WSL2 Ubuntu; tested on Linux with Node.js 22.

## Run (WSL Ubuntu)

```bash
cd Restaurant-Chain-Mockup
node --version
npm start
```

Open **http://localhost:3000** in your browser, then refresh to regenerate the mock companies.
Press `Ctrl+C` in the terminal to stop the server.

You can override the port with `PORT=8080 npm start`.

### Exports

- `http://localhost:3000/export.md` — Markdown sample data (newly generated on request)
- `http://localhost:3000/export.txt` — plain text sample data

### Tests

```bash
npm test
node --check src/models.js
node --check src/server.js
```

## Directory structure

```text
Restaurant-Chain-Mockup/
├── src/models.js           # Model classes, conversion interface, random generators
├── src/server.js           # HTTP server and HTML document
├── tests/models.test.js    # Automated unit and HTTP tests
├── docs/requirements.md    # Requirements and interpretations
├── docs/test-report.md     # Actual verification outcomes
├── learning/              # Japanese layered learning materials
├── .gitignore
├── package.json
└── README.md
```

## GitHub publishing

This project has **not** been pushed to GitHub for you. Create a public repository only after checking its contents:

```bash
git init
git status
git add .
git commit -m "Build restaurant chain mockup"
git branch -M main
# Create an empty public GitHub repository in the website first.
git remote add origin https://github.com/YOUR_USERNAME/Restaurant-Chain-Mockup.git
git push -u origin main
```

Alternatively, with GitHub CLI installed and authenticated (`gh auth login`):

```bash
gh repo create Restaurant-Chain-Mockup --public --source=. --remote=origin --push
```

`gh repo create ... --push` should be used after you have committed locally. Do not set a second remote called `origin` if it already exists. Use `git remote -v` to inspect it. For existing repositories, run `git status`, `git add .`, `git commit`, and `git push` rather than creating a second repository. HTTPS pushes authenticate with an account token/credential manager, not an account password; SSH uses an SSH key registered on GitHub. If push fails with `remote origin already exists`, inspect with `git remote -v` and change with `git remote set-url origin ...` if appropriate.

After pushing, inspect the GitHub file list and paste your public repository URL into the Recursion submission field.

## Scope and assumptions

No provided starter code or exact screenshot source code was available. `FileConvertible` is represented by an abstract-like class in JavaScript; unlike PHP/TypeScript, JavaScript has no native `interface` declaration. The `RestaurantLocation` and `User` field sets are reasonable assumptions where the screenshot did not fully specify them. These choices are documented in `docs/requirements.md`.

Public-source note: this repository contains only generated/mock entities; no user-specific information or credentials.
