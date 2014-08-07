clean:
	find . -name "*~" -exec rm -rf {} \;
	rm -rf *.log
	rm -rf *.txt
	rm -rf *.pyc

cleandata:
	@rm -rf tmp/* data/* recycle/*

cleanall:clean cleandata

commit:
	@echo "Commiting changes..."
	@git commit -am "Commit"
	@git push origin master

pull:
	@echo "Pulling from repository..."
	@git reset --hard HEAD	
	@git pull
	@chown -R www-data.www-data .

create:
	@echo "Resetting tables..."
	@mysql --user='root' -p < tables.sql

permissions:
	@chown -R www-data.www-data .
