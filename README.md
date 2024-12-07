# Task

Take a variable number of identically structured json records and de-duplicate the set.

An example file of records is given in the accompanying 'leads.json'.  Output should be same format, with dups reconciled according to the following rules:

1. The data from the newest date should be preferred.

2. Duplicate IDs count as dups. Duplicate emails count as dups. Both must be unique in our dataset. Duplicate values elsewhere do not count as dups.

3. If the dates are identical the data from the record provided last in the list should be preferred.

Simplifying assumption: the program can do everything in memory (don't worry about large files).

The application should also provide a log of changes including some representation of the source record, the output record and the individual field changes (value from and value to) for each field.

Please implement as a command line program.

---

# Notes

## from the dev/applicant
- 

---

# Setup guide

- this program uses php, so running it will require having PHP installed <br/> 
Ideally version `8.2+`

- make sure to run `composer install` to install the necessary packages <br/>
packages used:
    - [pest](https://pestphp.com/) - for testing
    - [Symfony console](https://symfony.com/doc/current/components/console.html) - for formatting console output
    - [justinrainbow/json-schema](https://github.com/jsonrainbow/json-schema) - for validating json schema
    
- running tests:
    ```bash
    php vendor/bin/pest
    ```
    ![unit test](/assets/test_sample.png)

- running the command line program:
    ```bash
    php parser.php {file path here}
    ```
    <u>example</u>:
    ![script ex](/assets/script_run.png)
    1. Running the command line program. In this example, the leads data, `leads.json`, was added to the project root

    2. The updated json file can be found in the `storage/leads` directory.

    3. The log containing updates made can be found in the `storage/logs/leads` directory.
