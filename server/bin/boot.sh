$KAFKA_HOME/bin/zookeeper-server-start.sh $KAFKA_HOME/config/zookeeper.properties 2>&1 &
$KAFKA_HOME/bin/kafka-server-start.sh $KAFKA_HOME/config/server.properties 2>&1 &

sleep 4

$KAFKA_HOME/bin/kafka-topics.sh --create --zookeeper localhost:2181 --replication-factor 1 --partitions 1 --topic symfonyTasks
$KAFKA_HOME/bin/kafka-topics.sh --create --zookeeper localhost:2181 --replication-factor 1 --partitions 1 --topic symfonyStats

$KAFKA_HOME/bin/kafka-topics.sh --list --zookeeper localhost:2181
java -jar /target/symfony-consumer-2.0.jar
