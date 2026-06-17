## Providers (Public)
| Method | URL | Name | Description |
|--------|-----|------|-------------|
| GET | /providers | providers.index | List active providers |
| GET | /providers/{provider} | providers.show | Provider detail page |

## Providers (Admin)
| Method | URL | Name | Description |
|--------|-----|------|-------------|
| GET | /admin/providers | admin.providers.index | List all providers |
| GET | /admin/providers/create | admin.providers.create | Create form |
| POST | /admin/providers | admin.providers.store | Store new provider |
| GET | /admin/providers/{provider}/edit | admin.providers.edit | Edit form |
| PUT | /admin/providers/{provider} | admin.providers.update | Update provider |
| DELETE | /admin/providers/{provider} | admin.providers.destroy | Delete provider |
