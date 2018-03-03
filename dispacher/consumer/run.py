import subprocess
subprocess.call("mvn package",shell=True)
subprocess.call("java -jar target/symfony-consumer-2.0.jar",shell=True)