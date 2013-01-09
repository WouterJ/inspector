Feature: Inspector Command
    In order to find files
    Users should be able to
    list the found files

    Scenario: Inspector looks for strings
        Given I am in a directory called "test"
        And I have a file called "foo.txt" which contains "hello world"
        And I have a file called "bar.php" which contains "echo 'foo world';"
        And I have a file called "baz.txt" which contains "foobar"
        When I run "inspect" with "-p world"
        Then I should get:
            """
            id  file
            ==  ===
            1   bar.php
            2   foo.txt
            """
