<?php

function mediaIsAbsoluteUrl($path) {
    $path = trim((string)$path);
    if ($path === '') {
        return false;
    }

    if (stripos($path, 'http://') === 0 || stripos($path, 'https://') === 0) {
        return true;
    }

    if (stripos($path, 'data:') === 0) {
        return true;
    }

    if (strpos($path, '//') === 0) {
        return true;
    }

    return false;
}

function mediaUrlFromPath($path, $fallbackPath = '/IMAGES/live/hero/hero-fallback.png') {
    $path = trim((string)$path);
    if ($path === '') {
        return appUrl($fallbackPath);
    }

    if (mediaIsAbsoluteUrl($path)) {
        $host = strtolower((string)parse_url($path, PHP_URL_HOST));
        // `source.unsplash.com` URLs are legacy and frequently return non-image responses.
        if ($host === 'source.unsplash.com') {
            return appUrl($fallbackPath);
        }
        return $path;
    }

    if ($path[0] === '/') {
        if (!mediaPublicAssetExists($path)) {
            return appUrl($fallbackPath);
        }
        return appUrl($path);
    }

    if (!mediaPublicAssetExists('/' . ltrim($path, '/'))) {
        return appUrl($fallbackPath);
    }

    return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
}

function mediaPublicAssetExists($path) {
    $normalized = '/' . ltrim((string)$path, '/');
    $root = realpath(__DIR__ . '/..');
    if ($root === false) {
        return false;
    }

    return is_file($root . $normalized);
}

function roomImageBucketFromType($roomType, $roomNo = null) {
    $type = strtolower(trim((string)$roomType));

    if ($type !== '') {
        if (strpos($type, 'premium') !== false || strpos($type, 'vip') !== false || strpos($type, 'presidential') !== false) {
            return 'vip';
        }
        if (strpos($type, 'family') !== false) {
            return 'family';
        }
        if (strpos($type, 'suite') !== false) {
            return 'suite';
        }
        if (strpos($type, 'deluxe') !== false) {
            return 'deluxe';
        }
        if (strpos($type, 'standard') !== false) {
            return 'standard';
        }
    }

    $floor = (int)substr((string)$roomNo, 0, 1);
    if ($floor === 5) {
        return 'vip';
    }
    if ($floor === 4) {
        return 'family';
    }
    if ($floor === 3) {
        return 'suite';
    }
    if ($floor === 2) {
        return 'deluxe';
    }

    return 'standard';
}

function roomFallbackPath($roomType, $roomNo = null) {
    $bucket = roomImageBucketFromType($roomType, $roomNo);
    switch ($bucket) {
        case 'vip':
            return '/IMAGES/live/rooms/vip/vip-suite.png';
        case 'family':
            return '/IMAGES/live/rooms/family/family-room.png';
        case 'suite':
            return '/IMAGES/live/rooms/suite/suite-room.png';
        case 'deluxe':
            return '/IMAGES/live/rooms/deluxe/deluxe-room.png';
        case 'standard':
        default:
            return '/IMAGES/live/rooms/standard/standard-room.png';
    }
}

function resolveRoomImageUrl($imagePath, $roomType, $roomNo = null) {
    return mediaUrlFromPath($imagePath, roomFallbackPath($roomType, $roomNo));
}

function diningBucketFromCategory($categoryName) {
    $category = strtolower(trim((string)$categoryName));

    switch ($category) {
        case 'starters':
            return 'starters';
        case 'soups':
            return 'soups';
        case 'main course':
            return 'mains';
        case 'breads':
        case 'rice':
            return 'breads_rice';
        case 'desserts':
            return 'desserts';
        case 'beverages':
            return 'beverages';
        case 'combos':
            return 'combos';
        default:
            return 'mains';
    }
}

function diningFallbackPath($categoryName) {
    $fallback = '/IMAGES/live/food/default_food.jpg';
    if (mediaPublicAssetExists($fallback)) {
        return $fallback;
    }
    $fallbackPng = '/IMAGES/live/food/default_food.png';
    if (mediaPublicAssetExists($fallbackPng)) {
        return $fallbackPng;
    }
    return '/IMAGES/live/no-food.png';
}

function diningImageNormalize($value, $compact = false) {
    $value = trim((string)$value);
    if ($value === '') {
        return '';
    }

    $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
    if ($ascii !== false) {
        $value = $ascii;
    }

    $value = strtolower($value);
    $value = str_replace(['&', '+'], ' and ', $value);
    $value = preg_replace('/[^a-z0-9]+/', ' ', $value);
    $value = preg_replace('/\s+/', ' ', (string)$value);
    $value = trim((string)$value);

    if ($compact) {
        return str_replace(' ', '', $value);
    }

    return $value;
}

function diningImageIndex() {
    static $index = null;
    if ($index !== null) {
        return $index;
    }

    $index = [];
    $root = realpath(__DIR__ . '/..');
    if ($root === false) {
        return $index;
    }

    $dirs = [
        ['abs' => $root . '/IMAGES/live/food', 'web' => '/IMAGES/live/food'],
        // Legacy compatibility for older imports.
        ['abs' => $root . '/IMAGES/live/dining_images', 'web' => '/IMAGES/live/dining_images'],
        ['abs' => $root . '/IMAGES/live/Dining images', 'web' => '/IMAGES/live/Dining images']
    ];

    foreach ($dirs as $dirMeta) {
        $dir = $dirMeta['abs'];
        $webPrefix = $dirMeta['web'];
        if (!is_dir($dir)) {
            continue;
        }

        $entries = @scandir($dir);
        if (!is_array($entries)) {
            continue;
        }

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $full = $dir . '/' . $entry;
            if (!is_file($full)) {
                continue;
            }

            $ext = strtolower((string)pathinfo($entry, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                continue;
            }

            $name = (string)pathinfo($entry, PATHINFO_FILENAME);
            $path = $webPrefix . '/' . $entry;
            $keyA = diningImageNormalize($name, false);
            $keyB = diningImageNormalize($name, true);

            if ($keyA !== '' && !isset($index[$keyA])) {
                $index[$keyA] = $path;
            }
            if ($keyB !== '' && !isset($index[$keyB])) {
                $index[$keyB] = $path;
            }
        }
    }

    return $index;
}

function diningImagePathByDishName($dishName) {
    $dishName = trim((string)$dishName);
    if ($dishName === '') {
        return '';
    }

    $root = realpath(__DIR__ . '/..');
    if ($root === false) {
        return '';
    }

    // Preferred deterministic paths.
    $deterministicBases = [
        '/IMAGES/live/food/' . $dishName,
        '/IMAGES/live/dining_images/' . $dishName,
        '/IMAGES/live/Dining images/' . $dishName
    ];
    foreach ($deterministicBases as $base) {
        foreach (['.jpg', '.jpeg', '.png', '.webp'] as $ext) {
            $candidate = $base . $ext;
            if (mediaPublicAssetExists($candidate)) {
                return $candidate;
            }
        }
    }

    // Case/special-character tolerant lookup.
    $index = diningImageIndex();
    $keyA = diningImageNormalize($dishName, false);
    $keyB = diningImageNormalize($dishName, true);

    if ($keyA !== '' && isset($index[$keyA])) {
        return $index[$keyA];
    }
    if ($keyB !== '' && isset($index[$keyB])) {
        return $index[$keyB];
    }

    return '';
}

function resolveDiningImageUrl($imagePath, $categoryName, $dishName = '') {
    $imagePath = trim((string)$imagePath);
    $dishName = trim((string)$dishName);

    // Highest priority: local mapped-by-name asset.
    if ($dishName !== '') {
        $mappedPath = diningImagePathByDishName($dishName);
        if ($mappedPath !== '') {
            return appUrl($mappedPath);
        }
    }

    if ($imagePath !== '' && !mediaIsAbsoluteUrl($imagePath)) {
        $normalized = '/' . ltrim($imagePath, '/');
        $allowedPrefixes = [
            '/IMAGES/live/food/',
            '/IMAGES/live/dining_images/',
            '/IMAGES/live/Dining images/'
        ];
        foreach ($allowedPrefixes as $prefix) {
            if (strpos($normalized, $prefix) === 0 && mediaPublicAssetExists($normalized)) {
                return appUrl($normalized);
            }
        }

        // If a legacy DB path points to missing file, try remapping by basename.
        $basename = (string)pathinfo($normalized, PATHINFO_FILENAME);
        if ($basename !== '') {
            $mapped = diningImagePathByDishName($basename);
            if ($mapped !== '') {
                return appUrl($mapped);
            }
        }

        if (mediaPublicAssetExists($normalized)) {
            return appUrl($normalized);
        }
    }

    return appUrl(diningFallbackPath($categoryName));
}
