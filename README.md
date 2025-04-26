# SEA-TEST

What is SEA-TEST about? It is a exam center tailored for VATSEA to assess students initially on their new ratings.

## Tech Stack:

This is pure laravel based application and it does not use any other stack.

## Dependencies:

1. php(version 8.2 and above)
2. composer
3. npm

## Commands to be run:

### Dependency management commands

For this application to fully function, we need a few commands to be run so that laravel handles everything as it is supposed to.

1. `composer install` - This command creates a directory named 'vendor' inside of the main project folder and it contains all the dependencies that laravel needs. (Have to run only when application is setup initially or when a new dependency is added)

2. `npm install` - Laravel by default uses tailwind for the frontend. npm is a frontend dependency manager that comes along with the nodejs package. This creates a folder named 'node_modules' which is packed with all the frontend dependencies. (Have to run only when application is setup initially or when a new dependency is added)

### Application related commands

1. `php artisan migrate` - This is used to create all the required tables in the database. This command works only when you give the correct credentials of the database inside the .env file. (Have to run only when application is setup initially or when a new migration is added). **For reference:** Migrations are nothing but blueprints for databases.

2. `php artisan key:generate` - This is used to set the APP_KEY variable in the .env file. (Have to run only when application is setup initially)

3. `php artisan queue:work` - This is queue worker command used to queue emails or jobs. For example, emails/jobs are queued into the database driver, then laravel daemon looks for any jobs available in the database, processes the email/job, once succeeds, then it is removed from the database. This is the heart of email sending and if for some reason, it stops, the entire email sending and internal job processing will stop.

4. `npm run dev/build` - The dev version of this command starts a vite server which I use for HMR(Hot modular replacement) meaning, when I do any change in application, the site is refreshed for me. **Note:** This makes the asset loading incredibly slow and the site takes very long to load as it serves the frontend assets via vite's live server. The build version of this command is used to bundle all the assets into a /dist/ folder and serves the frontend extremely fast because it has compiled all the CSS.

5. `php artisan serve` - This command brings up the application to 127.0.0.1 but in production environments I don't really use this, rather I write a nginx config file and it handles the stuff.