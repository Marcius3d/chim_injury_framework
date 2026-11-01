# Address Library format 5 (0.3.4)

## Why

`CHIMInjury.dll` was built against CommonLibSSE-NG from the colorglass vcpkg registry (about 3.7.0). That library only accepts address library formats 1 and 2. On startup it stops with `Unsupported address library format: 5` when the installed database is format 5.

Address Library for SKSE Plugins, All-in-One v13, ships Skyrim 1.7.x databases in format 5:

- `Data/SKSE/Plugins/versionlib-1-7-99-0.bin`
- `Data/SKSE/Plugins/versionlib-1-7-104-0.bin`

SKSE 2.3.x, which 1.7.x uses, also refuses a plugin that does not set `kVersionIndependentEx_AddressLibraryV5` in the exported `SKSEPlugin_Version` data. 1.7.104 needs SKSE 2.3.1 or newer.

SE 1.5.97 (format 1, `version-1-5-97-0.bin`) and AE 1.6.x (format 2, `versionlib-<version>.bin`) have to keep working. Nolvus on 1.5.97 is the install where 0.3.3 already loaded.

## What changed

The plugin now compiles against [CommonLibSSE-NG](https://github.com/alandtse/CommonLibSSE-NG) **15.4.0** (alandtse fork, `ng`, commit `adebc31`), taken as the git submodule `SkyrimPlugin/extern/CommonLibSSE-NG`. That is the same approach JContainers-NG and Community Shaders used: the colorglass port is too old, so the current library is built with the plugin.

- `SkyrimPlugin/CMakeLists.txt` adds the submodule instead of `find_package(CommonLibSSE)`. SE and AE are on. VR is off, so the nested openvr submodule is not required to compile. The GitHub Actions checkout still uses `submodules: recursive`.
- `add_commonlibsse_plugin(... USE_ADDRESS_LIBRARY)` is unchanged. In 15.4.0 that generates `SKSEPlugin_Version` with Address Library compatibility and `kVersionIndependentEx_AddressLibraryV5` (independent structs, which this plugin already asked for). The loader accepts format 5 when that is the byte at the start of the database, and still accepts formats 1 and 2.
- vcpkg no longer pulls `commonlibsse-ng` from colorglass. It only supplies the libraries CommonLibSSE-NG 15.4.0 builds against (fmt 12.1.0, spdlog 1.16.0, DirectXMath, DirectXTK, rapidcsv), from vcpkg baseline `ee12231b`, the baseline that release declares.
- The plugin version is 0.3.4 in the usual places (`manifest.json`, `dwemer-package.json`, `SkyrimPlugin/CMakeLists.txt`, `SkyrimPlugin/vcpkg.json`, `kPluginVersion`, `lib/chim_injury.php`).

Wounds, the SKSE co-save, RaceMenu overlays, CHIM messages and the server plugin behave as before. The only intended difference is which game versions the DLL can load on.

CommonLibSSE-NG 15.4.0 is GPL-3.0-or-later with a modding exception. The plugin's own code stays under the MIT licence in `LICENSE`.

## How this was tested

- `python3 scripts/build_packages.py --check-only` — the version strings match.
- `php -l lib/chim_injury.php` and `php tests/server_policy_test.php`.
- GitHub Actions workflow `Build` on this branch. The Windows job compiles `CHIMInjury.dll` and uploads the artifact `CHIM-Injury-Framework-build`.

A green build only shows that the DLL compiles and links. It does not show that wounds appear in Skyrim.

## Still needs in-game testing

1. **Skyrim SE 1.5.97** (Nolvus), where 0.3.3 loaded. After installing 0.3.4 over it, with Address Library v13: the save should reach the game, `CHIM-injury: activated` should show, and a wound should still be created, drawn when RaceMenu is installed, stored in the save, and reported to CHIM.
2. **Skyrim 1.7.104** with SKSE 2.3.1 or newer and Address Library All-in-One v13. The game should reach the main menu with no `Unsupported address library format: 5` dialog and no SKSE rejection about the address library version. The same wound checks as above should pass.

AE 1.6.x is still a supported target and is worth the same check if you have that runtime, but the two installs above are the ones this update is specifically for.
