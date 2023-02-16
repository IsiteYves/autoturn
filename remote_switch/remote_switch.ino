#include <ESP8266WebServer.h>
#include <ESP8266HTTPClient.h>
WiFiClient wifiClient;
String ssid = "RCA-WiFii";
String password = "@rca@2023";

int relayPin = D7;
ESP8266WebServer server;

//Light sensor is connected to the pin A0 (Analog 0)
int lightSensorPin = A0;
int lightIntensity;

String payload = "{\"deviceip\":";

void turnOnOrOff(String request) {
  Serial.println(request);
  int statIndex = request.indexOf("datastatus");
  if (statIndex != -1) {
    String statusStr = request.substring(statIndex);
    int value = statusStr.indexOf("HIGH");
    if (value > 0) {
      Serial.println("LESS: " + (String)value);
      digitalWrite(relayPin, LOW);
    } else {
      value = statusStr.indexOf("LOW");
      if (value < 0) {
        Serial.println("INVALID");
      } else {
        Serial.println("HIGH: " + (String)value);
        digitalWrite(relayPin, HIGH);
      }
    }
  }
}

void setup() {
  pinMode(relayPin, OUTPUT);
  digitalWrite(relayPin, LOW);
  Serial.begin(9600);

  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    digitalWrite(relayPin, HIGH);
    Serial.println("Connecting to WiFi...");
  }
  Serial.println("Connected to WiFi");
  Serial.print("IP: ");
  Serial.print(WiFi.localIP());
  payload += "\"" + WiFi.localIP().toString() + "\"}";
  server.begin();
}

void loop() {
  HTTPClient http;
  http.begin(wifiClient, "http://iot.benax.rw/projects/b9a31af7237a309d8f2654f837fd2871/autoturn/utils/check_light_status.php");
  // http.addHeader("Content-Type", "application/json");  // set the content type
  int httpCode = http.GET();
//  if (httpCode > 0) {
    String response = http.getString();
    Serial.println("successresponse..." + response+","+(String)httpCode);
//  }
//  else {
//    Serial.println("Error on HTTP request");
//  }
  http.end();
  delay(500);
  //  lightIntensity = analogRead(lightSensorPin);
  // Serial.println("light..." + (String) lightIntensity);
  //  if (lightIntensity < 15) {
  //    digitalWrite(relayPin, HIGH);
  //  } else {
  //    digitalWrite(relayPin, LOW);
  //  }
}
