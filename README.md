# survos/key-value-bundle — Typed String Set

> **Naming note:** Despite the name, this is **not** a key→value store (Redis-style).
> It is a **typed string set** — a database-backed collection of plain strings grouped
> by a named list. The only question it answers is: *"is X in set Y?"*

## Origin

Split from `survos/bad-bot-bundle`, which needed a database-backed list of path patterns
and IP addresses to block. The original use case: load banned strings at runtime (no redeploy),
check membership in a request listener.

The bundle was extracted with the intention of generalising it. That hasn't happened yet —
**nothing in the ecosystem currently uses it** outside of bad-bot's original pattern.

## What it is

A single `KeyValue` entity with two fields:

| Field  | Meaning                                     | Example          |
|--------|---------------------------------------------|------------------|
| `type` | The set name (which list does this belong to?) | `bad_bot_path`   |
| `value`| The member string                           | `wp-admin`       |

A unique constraint on `(type, value)` prevents duplicates. That's the whole data model.

**Supported operations:**

```php
$kv->has('wp-admin', 'bad_bot_path');        // true/false — membership check
$kv->add('wp-admin', 'bad_bot_path');        // add a member
$kv->remove('wp-admin', 'bad_bot_path');     // remove a member
$kv->getList('bad_bot_path');                // all members of a set
$kv->getTypes();                             // all set names + counts
```

## What it is NOT

- **Not a key→value map.** You cannot store `"spa:fotografia" → "photograph:1.0"`.
  If you need a lookup cache, build a dedicated entity in your app (see gist's
  `TranslationCache` as an example).
- **Not a vocabulary with labels.** If you need display labels, sort order, or
  extra metadata per term, use `folio-bundle`'s `TermSet`/`Term` entities.
- **Not a runtime enum replacement.** If values are fixed at deploy time, a PHP
  `enum` or Symfony's `Choice` constraint is simpler and type-safe.

## When to use it

Use this bundle when you need all three of:

1. The list must be editable at runtime (no redeploy)
2. Only membership matters — no labels, no metadata, no ordering
3. You want a shared DB store rather than per-app config

**Classic fit:** bot path patterns, banned IPs, banned emails — lists that ops teams
extend via console commands without touching code.

## Shape concern (open question)

The field names `type` and `value` are confusing at the PHP level (`$kv->type` reads
like a PHP type annotation, not a list name). Better names would be `setName`/`entry`
or `listCode`/`term`. This has not been renamed because nothing outside the bundle
depends on these properties directly — it can be done when there is a real consumer.

## Console commands

```bash
bin/console survos:kv:add    <value> [type]   # add a member
bin/console survos:kv:remove <value> [type]   # remove a member
bin/console survos:kv:show   [type]            # list members or all sets
```

## Installation

```bash
composer require survos/key-value-bundle
bin/console doctrine:schema:update --force    # SQLite / dev only
```

Configure a default list (optional):

```yaml
# config/packages/survos_key_value.yaml
survos_key_value:
    default_list: bad_bot_path
```

## Relationship to bad-bot-bundle

`bad-bot-bundle` originally embedded its own list storage. After the split, it should
depend on this bundle and use `KeyValueManagerInterface` for its path-pattern and IP
lists. That wiring has not been done yet — bad-bot currently has no reference to this
bundle at all. If you wire it, remove the inline storage from bad-bot and replace with
`survos/key-value-bundle`.
