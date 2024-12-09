> [!NOTE]
> - Going over how the app works: [loom video link #1](https://www.loom.com/share/595b7c19e3c34e5db15216f9d6184ffa?sid=c6085c71-3fb0-407b-9d2d-53a876977fae)
> - Explanation on some of decisions made around app logic based on the rules provided: [loom video link #2](https://www.loom.com/share/6fcae7ca1fba45f383bf61fa6411be45?sid=70b36b7f-3d87-45df-8112-c924678c410d) 

# Task

Take a variable number of identically structured json records and de-duplicate the set.

An example file of records is given in the accompanying 'leads.json'.  Output should be same format, with dups reconciled according to the following rules:

1. The data from the newest date should be preferred.

2. Duplicate IDs count as dups. Duplicate emails count as dups. Both must be unique in our dataset. Duplicate values elsewhere do not count as dups.

3. If the dates are identical the data from the record provided last in the list should be preferred.

Simplifying assumption: the program can do everything in memory (don't worry about large files).

The application should also provide a log of changes including some representation of the source record, the output record and the individual field changes (value from and value to) for each field.

Please implement as a command line program.
![instructions](/assets/instructions.png)

# Setup guide

- This program uses php, so running it will require having PHP installed <br/> 
Ideally version `8.2+`

- The program users [composer](https://getcomposer.org/) as the dependency manager. This will also need to be installed to get the dependencies used for this application. <br/> 
Make sure to run `composer install` to install the necessary packages <br/>
Packages used:
    - [pest](https://pestphp.com/) - for testing
    - [Symfony console](https://symfony.com/doc/current/components/console.html) - for formatting console output
    - [justinrainbow/json-schema](https://github.com/jsonrainbow/json-schema) - for validating json schema

- Running tests:
    ```bash
    php vendor/bin/pest
    ```
    ![unit test](/assets/test_sample.png)

- Running the command line program:
    ```bash
    php parser.php {file path here}
    ```
    <ins>example</ins>:
    ![script ex](/assets/script_run.png)
    1. Running the command line program. <br/>In this example, the leads data, `leads.json`, was added to the project root

    2. The updated json file can be found in the `storage/leads` directory.

    3. The log containing updates made can be found in the `storage/logs/leads` directory.

# Notes from the dev

> [!NOTE]
> I sought to get some clarification on certain parts of the specification provided, and I was advised to make personal call on the best route to move forward. Below are notes on some of the decisions made.

---

<ins>Rule #2</ins>:
> Duplicate IDs count as dups. Duplicate emails count as dups. Both must be unique in our dataset. Duplicate values elsewhere do not count as dups.

<i>I interpreted this to mean that the leads would be considered as duplicates if either</i> `id` <b>OR</b> `email` 
<i> field was duplicated rather than both fields needing to be duplicated.</i>

---

<ins>Consolidating the duplicates</ins>
> The application should also provide a log of changes including some representation of the source record, the output record and the individual field changes (value from and value to) for each field.

<i>Based on</i> "...the individual field changes (value from and value to) for each field" <i>I thought the consolidation could be interpreted as one of the following:</i>

1. The `leads` array does not remove the duplicated record but updates the individual field of the `lead` data <br/>
    example: <br/>
    <ins>before</ins>
    ```json
    {
        "leads": [
            {
                "_id": "jkj238238jdsnfsj23",
                "email": "foo@bar.com",
                "firstName": "John",
                "lastName": "Smith",
                "address": "123 Street St",
                "entryDate": "2014-05-07T17:30:20+00:00"
            },
            {
                "_id": "jkj238238jdsnfsj23",
                "email": "coo@bar.com",
                "firstName": "Ted",
                "lastName": "Jones",
                "address": "456 Neat St",
                "entryDate": "2014-05-07T17:32:20+00:00"
            }
        ]
    }
    ```
    <ins>after</ins>
    ```json
    {
        "leads": [
            {
                "_id": "jkj238238jdsnfsj23",
                "email": "coo@bar.com",
                "firstName": "Ted",
                "lastName": "Jones",
                "address": "456 Neat St",
                "entryDate": "2014-05-07T17:30:20+00:00"
            },
            {
                "_id": "jkj238238jdsnfsj23",
                "email": "coo@bar.com",
                "firstName": "Ted",
                "lastName": "Jones",
                "address": "456 Neat St",
                "entryDate": "2014-05-07T17:32:20+00:00"
            }
        ]
    }
    ```

2. The duplicate `lead` is removed from the final `leads` array <br/>
        example: <br/>
    <ins>before</ins>
    ```json
    {
        "leads": [
            {
                "_id": "jkj238238jdsnfsj23",
                "email": "foo@bar.com",
                "firstName": "John",
                "lastName": "Smith",
                "address": "123 Street St",
                "entryDate": "2014-05-07T17:30:20+00:00"
            },
            {
                "_id": "jkj238238jdsnfsj23",
                "email": "coo@bar.com",
                "firstName": "Ted",
                "lastName": "Jones",
                "address": "456 Neat St",
                "entryDate": "2014-05-07T17:32:20+00:00"
            }
        ]
    }
    ```
    <ins>after</ins>
    ```json
    {
        "leads": [
            {
                "_id": "jkj238238jdsnfsj23",
                "email": "coo@bar.com",
                "firstName": "Ted",
                "lastName": "Jones",
                "address": "456 Neat St",
                "entryDate": "2014-05-07T17:32:20+00:00"
            }
        ]
    }
    ```

<i>I chose to follow option</i> `#2` <i>which removes the duplicate record(s) from the array. Although option 1 does update the dupcliate records with the data that should be preferred, it seemed counter-intuitive with the overall goal since it would still leave duplicate records in the final output array.</i>
