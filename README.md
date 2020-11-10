# Movie Match Backend - public repository

This is the publicly available source code for the Movie Match backend - read more about the project [here](https://jonathan-kings.com/moviematch).

Please note - this public version of the code is slightly altered from what's actually running on the production environment. In particular:
- The .env file isn't made available.
- Code responsible for pulling in media data periodically has been stripped (in [RecommendationFetchController](app/Http/Controllers/RecommendationFetchController.php)) as I'd like to keep this private.
