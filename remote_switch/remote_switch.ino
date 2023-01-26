#include <ESP8266WiFi.h>

const char* ssid = "KAMARA-4E6A";
const char* password = "KAMARA450";

int relayPin = D7;
WiFiServer server(80);

void setup() {
  pinMode(relayPin, OUTPUT);
  Serial.begin(9600);
  digitalWrite(relayPin, LOW);

  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
  }
  Serial.println("Connected to WiFi");
  Serial.print("IP: ");
  Serial.print(WiFi.localIP());
  server.begin();
}

void loop() {
  WiFiClient client = server.available();
  if (client) {
    String request = client.readStringUntil('\r');
    client.flush();

    int statIndex = request.indexOf("datastatus=");
    if (statIndex != -1) {
      digitalWrite(relayPin, LOW);
      String statusStr = request.substring(statIndex);
      int value = statusStr.indexOf("HIGH");
      client.println("HTTP/1.1 200 OK");
      client.println("Access-Control-Allow-Origin: *");
      client.println("Access-Control-Allow-Methods: GET");
      client.println("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
      client.println("Content-Type: text/plain");
      client.println("Connection: close");
      client.println();
      if (value > 0) {
        digitalWrite(relayPin, HIGH);
        client.println("ON");
      } else {
        value = statusStr.indexOf("LOW");
        if (value > 0) {
          digitalWrite(relayPin, LOW);
          client.println("OFF");
        } else {
          client.println("INVALID CMD");
        }
      }
      client.stop();
    }
  }
}