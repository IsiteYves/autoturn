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
  payload += WiFi.localIP().toString();
  Serial.println("payload: " + payload);
}

void loop() {
  http.connect("iot.benax.rw", 80);
  http.println("POST /projects/b9a31af7237a309d8f2654f837fd2871/autoturn/utils/check_light_status.php HTTP/1.1");
  http.println("Host: iot.benax.rw");
  http.println("User-Agent: ESP8266/1.0");
  http.println("Content-Type: application/json");
  http.println("Content-Length: " + (String)payload.length());
  http.println();
  http.print(payload);
  String response = "";
  response += http.readStringUntil('\r');
  String payload2 = http.readString();
  Serial.println("response: " + payload2);
  turnOnOrOff(payload2);
  delay(2000);
}
