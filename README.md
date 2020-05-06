# PHP Task
Develop a web-based application for citizens and lawyers.
The application is an online registry for citizens who want to make appointments with lawyers

### Citizen Use Case:

- Register/Log-in
- Request an appointment with a authority by providing specific data: Name of the lawyer (existing in the system),
exact hour of the appointment;
- Receive, reschedule an rejected appointment and save confirmation for the appointment in the database;

### Lawyer Use Case:

- Register/Log-in
- Review all requests from citizens;
- Approve, reject or store requests in the database

### Requirements:

- Cover task use cases
    - Creating, retrieving, updating, deleting appointments
    - UI for handling citizen use cases
    - UI for handling lawyer use cases
- Resolving conflicting appointments
- Data validation and data sanitization
- Search, sort, filter
- Pagination
- Any libraries and frameworks are allowed
- Security enhancements (CSRF, XSS)

# Implementation

## Result - platform workflow  
1. Register citizens and register lawyers
2. Login with citizen and create appointment
3. Login with lawyer and agree or decline appointment (lawyer will see the appointment only if the citizen chose it at the time of creation) - if lawyer accept appointment process stop here
4. Login with citizen and create new appointment 
5. Login with lawyer and agree or decline appointment 
6. Login with citizen and view results

## Installation
1. git clone git@bitbucket.org:Lubo13/citizen-meet-lawyer.git
2. cd citizen-meet-lawyer
3. composer install
4. edit .env file and change DATABASE_URL with your respective settings
5. Execute: php bin/console doctrine:database:create
6. Execute: php bin/console doctrine:migration:migrate
7. Go to project in browser