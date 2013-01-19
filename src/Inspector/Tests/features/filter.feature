Feature: Inspector Filters
    In order to find the files we want
    Users should be able to
    filter files

    Scenario: Inspector can ignore files
        Given I am in a directory called "test"
          And I have a file called "foo.txt" which contains "hello world"
          And I have a file called "bar.php" which contains "echo 'foo world';"
          And I have a file called "cat.txt" which contains "lorem ipsum world"
         When I run "inspect" with "-p world --filter=*.txt"
         Then I should get:
            """
            id  file
            ==  ===
            1   bar.php
            """

    Scenario: Inspector has build-in filters
        Given I am in a directory called "test"
          And I have a file called "foo.txt" which contains "hello world"
          And I have a file called "bar.txt~" which contains "foo world"
          And I have a file called "cat.txt.swp" which contains "bar world"
          And I have a file called ".gitignore" which contains "*~\n*.swp"
         When I run "inspect" with "-p world"
         Then I should get:
            """
            id  file
            ==  ===
            1   foo.txt
            """
