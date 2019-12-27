
__demo-wait:
	#until ; do sleep 5;done
	for number in `seq 1 9` ; do \
        	echo -n .; \
        	curl --output /dev/null --silent --head --fail http://127.0.0.1:8080/server  && break ; \
            sleep 10; \
    	done

## Setup, install and run with fixtures
demo: setup start __demo-wait
demo:
	$(TOOLSDC) run --rm php-fpm make register
	$(TOOLSDC) run --rm php-fpm make fixtures
