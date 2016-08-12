BACKDIR=archive/backup
BACKFILE=microcurriculos

clean:
	@echo "Basic cleaning..."
	@find . -name "*~" -exec rm -rf {} \;
	@rm -rf *.log
	@rm -rf *.txt
	@rm -rf *.pyc

cleandata:clean
	@echo "Cleaning data..."
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

backup:
	@echo "Backuping Curriculo..."
	@bash backup.sh 

restore:
	@echo "Restoring table Quakes..."
	@-cat $(BACKDIR)/dump/$(BACKFILE)* > $(BACKDIR)/$(BACKFILE).tar.7z
	@-p7zip -d $(BACKDIR)/$(BACKFILE).tar.7z
	@-tar xf $(BACKDIR)/$(BACKFILE).tar
	@echo "Enter root mysql password..."
	@mysql -u root -p $(BACKFILE) < $(BACKDIR)/$(BACKFILE).sql
	@-rm $(BACKDIR)/$(BACKFILE)*

permissions:
	@chown -R www-data.www-data .

reset:cleanall
	$(shell echo "drop database Curriculo;drop user 'curriculo'@'localhost';" > /tmp/sql)
	@echo "Enter mysql root password:"
	@mysql -u root -p < /tmp/sql
