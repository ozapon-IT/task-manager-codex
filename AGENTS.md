# Repository Guidelines

## Project Structure & Module Organization
This workspace is intentionally lean: `package.json` pins Codex CLI as the sole tooling dependency and `node_modules/@openai/codex` houses the CLI harness. All application code you add should live under `src/` (create it when missing) with feature-specific subfolders (for example `src/tasks/TaskBoard.ts`). Shared utilities belong in `src/lib/` and test doubles in `tests/support/`. Keep assets such as fixtures or JSON seeds inside `assets/` to avoid polluting source directories.

## Build, Test, and Development Commands
Install dependencies with `npm install`, which restores the Codex CLI runtime. During development prefer `npx codex exec "npm run <script>"` so the agent can observe output. When you introduce build or test scripts, follow this pattern:
- `npm run dev` — launches your local sandbox (e.g., Vite or Next.js).
- `npm run build` — produces a production bundle and should be lint-clean.
- `npm test` — executes the unit suite; make sure it exits non-zero on failure.

## Coding Style & Naming Conventions
Default to TypeScript with 2-space indentation and single quotes. Use camelCase for variables/functions, PascalCase for React components or classes, and kebab-case for file names (`task-list.tsx`). Run Prettier (`npx prettier --check .`) and ESLint (`npx eslint src --max-warnings 0`) before opening a PR; add their configs if missing.

## Testing Guidelines
Pin to Vitest or Jest; place specs in `tests/` mirroring `src/` (`tests/tasks/taskBoard.spec.ts`). Mock external APIs with lightweight stubs rather than hitting live services. Aim for 80% branch coverage and document gaps in the PR description. Regenerate snapshots with `npm test -- -u` only when intentional.

## Commit & Pull Request Guidelines
Use Conventional Commits (`feat:`, `fix:`, `chore:`) so history stays searchable. Keep commits focused and avoid bundling unrelated changes. Pull requests must include: summary of behavior, screenshots or terminal logs when UI/CLI output changes, and references to tracked issues. Ensure CI scripts run locally and link to any follow-up work.

## Agent Workflow Notes
When collaborating with Codex, prefer incremental changes: expose plan steps, run read-only commands first, and request elevated permissions before mutations. Record non-obvious design choices in `docs/` so future agents can execute context-aware tasks without rediscovery.
