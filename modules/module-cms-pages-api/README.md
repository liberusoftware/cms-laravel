# CMS Pages API

Optional API presentation package for the Pages module. It exposes the
module-owned path, hierarchy, breadcrumb, alias, home/error, and template
read and write API under `/api/v1/cms/pages`.

Published pages expose their canonical path, breadcrumbs, aliases, home/error
flags, template, sanitized content, and featured media. Content writes require
the `content:write` ability. Alias and redirect mutations are validated by the
core Pages routing service; redirect sources cannot shadow canonical or alias
paths, and supported status codes are 301, 302, 307, and 308.
