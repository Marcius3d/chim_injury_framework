cmake_minimum_required(VERSION 3.21)

project(CHIMInjury VERSION 0.3.4 LANGUAGES CXX)

set(CMAKE_CXX_STANDARD 23)
set(CMAKE_CXX_STANDARD_REQUIRED ON)

# alandtse/CommonLibSSE-NG v15.4.0, pinned in extern/CommonLibSSE-NG.
# v15.4 reads Address Library formats 1, 2 and 5. add_commonlibsse_plugin()
# with USE_ADDRESS_LIBRARY declares AddressLibraryV5, which SKSE 2.3.x
# requires on Skyrim 1.7.x. SE and AE only; this plugin does not target VR.
set(ENABLE_SKYRIM_SE ON CACHE BOOL "CommonLibSSE: Skyrim SE" FORCE)
set(ENABLE_SKYRIM_AE ON CACHE BOOL "CommonLibSSE: Skyrim AE" FORCE)
set(ENABLE_SKYRIM_VR OFF CACHE BOOL "CommonLibSSE: Skyrim VR" FORCE)
set(BUILD_TESTS OFF CACHE BOOL "CommonLibSSE: unit tests" FORCE)
set(COMMONLIB_ENABLE_IPO OFF CACHE BOOL "CommonLibSSE: link-time optimization" FORCE)

add_subdirectory(${CMAKE_CURRENT_SOURCE_DIR}/extern/CommonLibSSE-NG EXCLUDE_FROM_ALL)
include(${CMAKE_CURRENT_SOURCE_DIR}/extern/CommonLibSSE-NG/cmake/CommonLibSSE.cmake)

# IPO for this plugin only. Set after the subdirectory so the static library
# is not built with link-time optimization.
set(CMAKE_INTERPROCEDURAL_OPTIMIZATION ON)

add_commonlibsse_plugin(${PROJECT_NAME}
    NAME "CHIMInjury"
    AUTHOR "Marcius3d"
    USE_ADDRESS_LIBRARY
    SOURCES src/main.cpp
)

target_precompile_headers(${PROJECT_NAME} PRIVATE src/PCH.h)

target_link_libraries(${PROJECT_NAME} PRIVATE winhttp version)

target_compile_definitions(${PROJECT_NAME}
    PRIVATE
        WIN32_LEAN_AND_MEAN
        NOMINMAX
)

if (MSVC)
    target_compile_options(${PROJECT_NAME} PRIVATE /W4 /permissive- /Zc:preprocessor /EHsc /bigobj /utf-8)
endif ()
