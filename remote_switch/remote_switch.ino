#include<ESP8266WiFi.h>
WiFiClient http;
String ssid = "RCA-WiFii";
String password = "@rca@2023";

int relayPin = D7;

//Light sensor is connected to the pin A0 (Analog 0)
//int lightSensorPin = A0;
//int lightIntensity;

String payload = "deviceip=";

void turnOnOrOff(String request) {
  int statIndex = request.indexOf("datastatus");
  // String statusStr = request.substring(statIndex);
  int value = request.indexOf("ON");
  if (value > 0) {
    Serial.println("ON!");
    digitalWrite(relayPin, LOW);
  } else {
    Serial.println("LOW!");
    digitalWrite(relayPin, HIGH);
    //    value = request.indexOf("LOW");
    //    if (value < 0) {
    //      Serial.println("INVALID");
    //    } else {
    //    }
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
  payload += WiFi.localIP().toString();
}

void loop() {
  http.connect("iot.benax.rw", 80);
  http.println("GET /projects/b9a31af7237a309d8f2654f837fd2871/autoturn/utils/check_light_status.php HTTP/1.1");
  http.println("Host: iot.benax.rw");
  http.println("User-Agent: ESP8266/1.0");
  http.println("Content-Type: application/json");
  http.println("Content-Length: " + (String)payload.length());
  http.println();
  http.print(payload);
  String response = "";
  response += http.readStringUntil('\r');
  String payload2 = http.readString();
  turnOnOrOff(payload2);
  // delay(100);
}
