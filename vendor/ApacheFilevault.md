## Filevault

To export svn filesm use [filevault](https://github.com/apache/jackrabbit-filevault)

Build (one time)

    docker run -it --rm --name my-maven-project -v "$(pwd)":/usr/src/mymaven -w /usr/src/mymaven maven:3.3-jdk-8 mvn clean install

unzip Bin and put into vendor directory
