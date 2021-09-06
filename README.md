### 10Web’s latest blog posts scraper
Web application that scrapes and aggregates 10Web’s latest blog posts and
shows them on the front page along with the topmost used word per day.

### Add-ons included
1. Search for blog posts on the front page
2. Filters by date for blog posts
3. Caching mechanism **Memcached**, cache expire time `2 min`
4. Responsive design

### Requirements
| Name | Version |
| ------ | ------ |
| `Docker ` | 20.10.8 |
| `Docker Compose` | 1.28.2 |
| `Git` | 2.20.1 |
| `Composer` | 2.0.9 |

# How to build
```sh
# Clone application
$ git clone https://github.com/Aramayis12/scrape_posts.git

# Install dependencies
$ cd ./scrape_posts && composer install

# Run docker-compose
$ docker-compose up -d

# Check docker-compose statuses if State column is Up for all containers continue with next command
$ docker-compose ps
```
If **Linux** run this command to enter container
```sh
docker exec -ti php /bin/bash
```
If **Windows** run this command to enter container
```sh
winpty docker exec -ti php sh
```

Run migrations and open permissions
```sh
# Run migrations and type yes, if error wait a min and try again 
php yii migrate

# change folder permission
chmod 777 web/assets runtime
```

# CLI scraper
CLI scraper that parses 10Web’s Blog website and fills the data to the database

# How to use
#### CLI Scraper Command Params
Example of command `php yii scrape-posts 20 2020-01-01/2021-10-01`

Params required *
* `limmit` _[integer]_ - Article limit, for **unlimit** type `0`.
* `start_date` _[string]_ - Start Date.
* `end_date` _[string]_ - End Date.

```sh
# Run script
php yii scrape-posts [limit] [start_date]/[end_date]
```

Open application in browser using http://localhost:20080/

if want to exit container type exit
```sh
# Exit from container
exit
```

To run script again need to enter to container again and run script as noted above
