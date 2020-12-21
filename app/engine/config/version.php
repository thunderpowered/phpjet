<?php

// Main engine version
// Increase only when significant changes leading to incompatibility with the previous code occur
// Example: the code has been completely rewritten, new architecture of project added etc.
define("ENGINE_VER_GLOBAL", 0);

// Functional version
// Increase only when significant changes leading to a change in functionality occur
// Example: new section of site was added, new feature in admin panel was added etc.
define("ENGINE_VER_FUNC", 1);

// Development version
// Increase only when significant changes not satisfying two points above
// Example: significant bug fixed, function was rewritten etc.
define("ENGINE_VER_DEV", 2);

// Development stage
// 0 - alpha
// 1 - beta
// 2 - release candidate
// 3 - release
define("ENGINE_VER_STAGE", 0);

// Name of the release
// Just to identify it, optional
// I like to use names of cosmic objects (constellations, nebulae etc.)
define("ENGINE_VER_RELEASE_NAME", "Taurus");