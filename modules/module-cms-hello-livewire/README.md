# CMS Hello Livewire

The `liberusoftware/module-cms-hello-livewire` package provides the Livewire 4
presentation adapter for `liberusoftware/cms-hello`.

It exposes the `cms-hello-livewire.greeting-list` component. Domain behavior and
persistence remain owned by the core Hello module; the component only queries
the public model boundary, paginates results, validates its display-page size,
and renders accessible empty-state and pagination controls.
