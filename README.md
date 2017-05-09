Todo Application
========================

* Users can register make their account. 
* Users Can manage their todo lists.
* Users will be notified 24 hours in advance with email with their todo.


Install
--------------

 - clone project 
 - configure database and mailer in app/config/parameters.yml
 - run command  ´´´php app/console doctrine:database:create´´´
 - run command  ´´´php app/console doctrine:schema:create´´´
 - create categoryes directly in database or run command
  ´´´php app/console doctrine:fixtures:load´´´

Run Tests
--------------
- run command  ´´´php app/console doctrine:database:create --env=test´´´
- run command  ´´´php app/console doctrine:schema:create --env=test´´´
- run command  ´´´phpunit´´´

Whats next
--------------

Roadmap:

  * optimize tests

  * optimize sending email notifications ( if there are more than 1 todo in next 24h bundle them in single email)

  * Add CRUD for categories;

  * Add profile page;

  * Add ability for users to schedule email notifications;
