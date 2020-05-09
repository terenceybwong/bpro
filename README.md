# BetterSoftware BPro integration proof-of-concept

This Symfony console command is built to demonstrate the feasibility
of building an integration with BetterSoftware BPro inegration.

Since it is a code not mean to be run in production, some software
engineering practices are not done, e.g. documentation, code
commenting, etc. And the code is not built to for reuse with lots
of flexibility.

It is built using Symfony console command component only.

## Installation

```
composer install
```

## Run integration

Show available operations:

```
php -e src/console
```

#### Output
```
Console Tool

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  help       Displays help for a command
  list       Lists commands
 job
  job:fetch  Fetch completed jobs
```

Fetch job options
```
php -e src/console  job:fetch -h
```

#### Output
```
Description:
  Fetch completed jobs

Usage:
  job:fetch [options]

Options:
  -x, --export-format=EXPORT-FORMAT  Export format (csv|json) [default: "csv"]
  -o, --output-format=OUTPUT-FORMAT  Output format (csv|json) [default: "csv"]
  -i, --instance=INSTANCE            Instance to login to [default: "instance1"]
  -h, --help                         Display this help message
  -q, --quiet                        Do not output any message
  -V, --version                      Display this application version
      --ansi                         Force ANSI output
      --no-ansi                      Disable ANSI output
  -n, --no-interaction               Do not ask any interactive question
  -v|vv|vvv, --verbose               Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```
