# CMS Livewire modules

The reference adapter `liberusoftware/module-cms-hello-livewire` demonstrates
the one-to-one boundary for Livewire 4. It depends on the Hello core package,
registers an explicit component, and keeps domain rules out of component state.
Additional adapters follow the same package naming and ownership decision in
[`docs/adr/0004-module-package-naming.md`](../../docs/adr/0004-module-package-naming.md).

Deferred frontend implementations remain outside this run. Do not add React,
Vue, Nuxt, React Native, Flutter, or other presentation indexes here.
