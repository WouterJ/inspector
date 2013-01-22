# Inspector

Inspector is a simple tool that can be used to search in a directory for a
given string or pattern.

## Installation

Inspector is available as a composer package on packagist. Installing it is
quite simple:

    $ composer create-project wouterj/inspector

## Usage

 > This is a quick documentation, advantage documentation is added during the
 > BETA period

Inspector has just one command at this moment, called `inspect`. This will
inspect a directory:

    # searches for files which contains 'hello'
    $ php inspector.php inspect -p 'hello'

This command has multiple options:

 * `--pattern` (`-p`): This is the pattern which you are looking for. (**required**)

       # searches for files which contains 'foo' or 'bar'
       $ php inspector.php inspect -p '/(foo|bar)/'

 * `--dir` (`-d`): This is the directory to search in, if this is empty it
   will search in the current directory.

       # searches in the %current_dir%/hello directory
       $ php inspector.php inspect -p 'foo' -d hello

 * `--filter` (`-f`): This can be a Regex, to determine which files should be
   ignored, or a name of one of the build in filters (more about this in [the
   filters section](#filters)).

       # ignores all php files
       $ php inspector.php inspect -p 'foo' -f '*.php'

### Filters

Inspector has one build-in filter at the moment. Filters provide a solution
for common `--filter` pattern.

#### GitIgnoreFilter

This filter will search for a `.gitignore` file in the root of the document
and ingores every file that is in there.

    $ php inspector.php inspect -p 'foo' --filter gitignore

## Contributing

Inspector love contributors. Please, fork this repo, create a new branch,
improve this code and open a PR. Inspector uses
[the Symfony Coding Standards](http://symfony.com/doc/current/contributing/code/standards.html)

If you do not want to contribute with writing code, you can also help to
review the issues/PRs.
