# Study Guide staging deployment

This deployment path publishes only the static two-chapter Study Guide to the
Basic-Auth-protected staging host. It does not modify Moodle, its database,
`moodledata`, Redis, cron, or production Apache configuration.

## Release model

- GitHub Actions builds `study-guide-<commit>.tar.gz` from `study-guide/`.
- The build rejects symlinks and PHP-like executable files.
- GitHub verifies the SSH host key instead of disabling host-key checking.
- The server verifies the SHA-256 checksum and archive paths.
- Each commit is extracted into an immutable release directory.
- `current` is switched atomically to the new release.
- Previous releases remain available for explicit rollback.

## GitHub environment

Create the protected environment `study-guide-staging` and add these secrets:

- `STAGING_DEPLOY_HOST`
- `STAGING_DEPLOY_PORT`
- `STAGING_DEPLOY_USER`
- `STAGING_SSH_PRIVATE_KEY`
- `STAGING_SSH_KNOWN_HOSTS`
- `STAGING_BASIC_USER`
- `STAGING_BASIC_PASSWORD`

Use a dedicated SSH key for the deployment user. Capture the host key from a
trusted administrator session; do not run `ssh-keyscan` inside the workflow.

## Server installation

Install `deploy-study-guide` as `/usr/local/bin/deploy-study-guide`, owned by
root and mode `0755`. The deployment user must own:

- `/var/lib/acupuncture-deploy/incoming`
- `/var/www/acupuncturetests-staging`

Apache only needs group read/traverse access through the `www-data` group.

## Manual rollback

List releases:

```bash
sudo -u deploy-acupuncture /usr/local/bin/deploy-study-guide status
```

Activate a prior commit:

```bash
sudo -u deploy-acupuncture /usr/local/bin/deploy-study-guide rollback COMMIT_SHA
```

Rollback only switches the static Study Guide symlink. It does not touch
Moodle or any database.
