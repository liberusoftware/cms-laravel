# Video and Audio

The domain module owns tenant-scoped audiovisual assets, upload and remote sources, provider-neutral transcoding adapters, posters, chapters, captions, transcripts, streaming URIs, and playback metadata.

Register a `TranscodingAdapterInterface` implementation with `TranscodingAdapterRegistry`; the domain service handles validation, idempotent variants, lifecycle state, and failure recording without depending on a provider SDK.
