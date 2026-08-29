# Drupal Migration

The Drupal Migration module adapts Drupal source records to Liberu CMS's resumable Migration Framework. It keeps source-specific orchestration separate from the shared job and record lifecycle so imports can be retried safely.

Use `DrupalMigrationService` to start a Drupal job, add typed source records, process each record, and complete the job after all records have reached a terminal state.
