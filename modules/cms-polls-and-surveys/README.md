# CMS Polls and Surveys

The domain boundary for tenant-scoped polls and surveys. It owns poll schedules, question types, branching rules, anonymous/authenticated response policy, aggregated results, privacy-filtered exports, and response erasure.

Use `PollService` for mutations and reads. Responses are stored with a keyed anonymous respondent hash; exports omit that identity data unless the caller has explicitly authorized an identity-bearing export.
