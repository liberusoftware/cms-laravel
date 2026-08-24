# Conformance Specification for cms-laravel

This document outlines the conformance status of the `cms-laravel` repository against the standards defined in [liberusoftware/documentation](https://github.com/liberusoftware/documentation), with a primary focus on [CMS.md](https://github.com/liberusoftware/documentation/blob/main/projects/cms/CMS.md).

## 1. Project Overview

The `cms-laravel` project is a composable content and digital-experience platform. Its architecture and features are expected to align with the Liberu documentation.

## 2. Conformance Analysis

This section records the repository state verified on 2026-08-24. It is a
conformance record, not a claim that every portfolio or release requirement is
complete.

### 2.1. Module Implementation Status

The following table summarizes the implementation status of the modules defined in `CMS.md`.

| Module             | Status          | Evidence |
| ------------------ | --------------- | -------- |
| CMS Core           | Implemented    | `modules/cms-contracts`, `modules/cms-core` |
| Content Entities   | Implemented    | `modules/cms-content`, `modules/cms-content-types` |
| Field System       | Implemented    | `modules/cms-content-types` |
| Taxonomy           | Implemented    | `modules/cms-posts` |
| Pages              | Implemented    | `modules/cms-pages` |
| Rich Text Editor   | Partial        | Host Blade/theme integration exists; no separate frontend package is generated |
| Block Editor       | Implemented    | `modules/cms-blocks` |
| Revisions          | Implemented    | `modules/cms-content` |
| Editorial Workflow | Implemented    | `modules/cms-content`, `modules/cms-posts` |
| Publishing         | Implemented    | Core content and delivery API tests |
| Media Library      | Implemented    | `modules/cms-media` |
| Navigation         | Implemented    | `modules/cms-menus` |

The corresponding API and Filament boundaries are indexed in
`modules/api/README.md` and `modules/filament/README.md`. There are currently
no separately released CMS Livewire packages; `modules/livewire/README.md`
records that deliberate boundary.

### 2.2. Architectural Divergences

*(This section should list any architectural patterns, coding standards, or other conventions that deviate from the Liberu documentation.)*

*   The application resolves package sources from local path repositories for
    development reproducibility. The 22 independently released package
    sources are also mirrored to their public `liberusoftware/module-*`
    repositories, each with a `v0.1.0` tag, protected `main`, and a blocking
    `package-quality` workflow. Packagist submission and public-index
    verification remain outstanding.
*   The full owned-source test suite passes (688 tests, 1,787 assertions), but
    the measured coverage is 96.1% of statements and 87.0% of methods. The
    required 100% gate is therefore intentionally blocking.
*   The final major release, independent package repositories, Packagist
    submissions, and production deployment verification remain outstanding.

The package inventory and repository URLs are recorded in
[`docs/PACKAGES.md`](PACKAGES.md). Packagist publication requires an approved
Packagist account/token or an authorized submission through the Packagist web
flow; no such credential is present in this environment.

## 3. Migration Plan

The following is a high-level plan to bring the `cms-laravel` repository into conformance.

1.  **Phase 1: Content Modeling and Core:** Implement the core CMS modules.
2.  **Phase 2: Authoring and Editorial:** Implement authoring and editorial features.
3.  **Phase 3: Media and Information Architecture:** Implement media and information architecture modules.

Detailed implementation tasks will be tracked in separate GitHub issues.
