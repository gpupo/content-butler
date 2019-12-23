
## Backup current files
backup@opt:
	docker cp "$(docker-compose ps -q content-server)":/opt/jackrabbit ./var/opt/
	printf "${COLOR_COMMENT}Backup done.${COLOR_RESET}\n"

## Backup current files
backup@svn-style:
	$(TOOLSDC) run --rm java bin/clone var/dest_directory;
	printf "${COLOR_COMMENT}Backup done.${COLOR_RESET}\n"
