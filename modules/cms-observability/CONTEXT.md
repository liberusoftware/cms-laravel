# CMS Observability

The operability layer over the CMS: it answers "is the system healthy right now?"
(readiness) and "what is it doing?" (metrics), as removable seams with zero-infra
defaults. It owns no domain data — feature modules contribute checks and the system
emits metrics through contracts this context defines.

## Language

**Liveness**:
Whether the application process has booted and can answer HTTP at all. Served by the
framework's `GET /up`; this context does not own it.
_Avoid_: health, uptime, ping

**Readiness**:
Whether the application's dependencies (database, cache, queue, search, storage) are
reachable right now, so the instance can safely take traffic. Served by
`GET /health/ready`.
_Avoid_: health check, status, liveness

**Health check**:
A single named dependency probe contributed to the readiness registry by the module
that owns that dependency. Reports a coarse ok/fail and declares its criticality.
_Avoid_: monitor, probe, test

**Check criticality**:
Whether a failing health check pulls the instance out of rotation (`critical` → 503)
or merely downgrades the reported status while still serving (`degraded` → 200). Only
the database is critical by default.
_Avoid_: severity, level, priority

**Metric recorder**:
The contract (`MetricsRecorderInterface`) through which any part of the CMS records a
counter, timing, or gauge, without knowing the backend. The default recorder writes to
an isolated log channel; an operator may bind a Pulse / StatsD / Prometheus recorder.
_Avoid_: metrics client, telemetry, tracker

**Metric**:
A single named, tagged measurement (`content.published`, `api.request` latency). Named
in stable dot-notation, analogous to event and filter names.
_Avoid_: stat, datapoint, signal
