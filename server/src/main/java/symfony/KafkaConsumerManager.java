package symfony;

import org.apache.kafka.clients.consumer.*;
import org.apache.kafka.clients.consumer.KafkaConsumer;

import java.util.Collections;
import java.util.Properties;


public class KafkaConsumerManager {

    private final static String TOPIC = "symfonyTasks";
    private final static String BOOTSTRAP_SERVERS = "127.0.0.1:9092";

    private static KafkaConsumer createConsumer() {
        final Properties props = new Properties();
        props.put(ConsumerConfig.BOOTSTRAP_SERVERS_CONFIG,BOOTSTRAP_SERVERS);
        props.put(ConsumerConfig.KEY_DESERIALIZER_CLASS_CONFIG,"org.apache.kafka.common.serialization.LongDeserializer");
        props.put(ConsumerConfig.VALUE_DESERIALIZER_CLASS_CONFIG,"org.apache.kafka.common.serialization.StringDeserializer");
        props.put(ConsumerConfig.GROUP_ID_CONFIG, "test");
        final KafkaConsumer<Long, String> consumer = new KafkaConsumer(props);
        consumer.subscribe(Collections.singletonList(TOPIC));
        return consumer;
    }

    static void runConsumer()  {

        final KafkaConsumer<Long, String> consumer = createConsumer();
        final int giveUp = 100;   int noRecordsCount = 0;

        while (true) {

            final ConsumerRecords<Long, String> consumerRecords = consumer.poll(1000);

            if (consumerRecords.count()==0) {
                noRecordsCount++;
                if (noRecordsCount > giveUp) break;
                else continue;
            }
            consumerRecords.forEach(record -> {
                System.out.printf("Consumer Record:(%d, %s, %d, %d)\n", record.key(), record.value(),record.partition(), record.offset());
            });
            consumer.commitAsync();
        }

        consumer.close();
        System.out.println("DONE");

    }
}
  

