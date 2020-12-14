# Contribution Guide

This contribution guide is a work-in-progress.

## Getting started

1. **Clone this repository**
    ```
    gh repo clone firevel/firestore-cache-driver # or
    git clone https://github.com/firevel/firestore-cache-driver
    ```
2. **Install the dependencies**
    ```
    composer install
    ```
3. **Run the unit tests**
   1. Using Docker
      1. `composer run test`
   2. Using the Firebase CLI (see below)
      1. `firebase emulators:start`
      2. `vendor/bin/phpunit` or `composer run test-only`

## Optional: Using the Firebase CLI

By default, when you run `composer run test`, Composer will spin up a Docker image
with a Firestore emulator (this one, to be exact).

If you would rather use features like the Firestore UI, you can use the [Firebase CLI][1] with the [Firestore Emulator][2].

The project comes pre-packages with a `firebase.json` that will bind the emulators to the correct ports, so after
you've set up the CLI, you can simply run `firebase emulators:start`.

[1]: https://firebase.google.com/docs/cli
[2]: https://firebase.google.com/docs/emulator-suite/install_and_configure
