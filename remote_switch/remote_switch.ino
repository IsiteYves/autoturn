#include <ESP8266WiFi.h>

const char* ssid = "KAMARA-4E6A";
const char* password = "KAMARA450";

int relayPin = D7;
WiFiServer server(80);

void setup() {
  pinMode(relayPin, OUTPUT);
  digitalWrite(relayPin, HIGH);
  digitalWrite(relayPin, LOW);
  Serial.begin(9600);

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
        Serial.println("LESS: " + (String)value);
        digitalWrite(relayPin, LOW);
        client.println("ON");
      } else {
        value = statusStr.indexOf("LOW");
        if (value < 0) {
          client.println("INVALID CMD");
        } else {
          Serial.println("STATOS: " + (String)value);
          digitalWrite(relayPin, HIGH);  
          client.println("OFF");
        }
      }
      client.stop();
    }
  }
}
