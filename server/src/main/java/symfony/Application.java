package symfony;

import symfony.KafkaConsumerManager;

public class Application {
    public static void main(String[] args) {
        KafkaConsumerManager.runConsumer();
        System.out.println("DONE");
    }
}