#include <Arduino.h>
#include <WiFiManager.h>
#include <HTTPClient.h>
#include <AceButton.h>
#include <IRremoteESP8266.h>
#include <IRrecv.h>
#include <IRutils.h>

WiFiManager wifiManager;

  WiFiManagerParameter deviceId("deciceId", "ACTIVATION ID", "kira's device", 40);

WiFiClient client;

const uint16_t irRecvPin = 35;

IRrecv irrecv(irRecvPin);

decode_results results;

boolean notConnected = true;

const int portalTrigerPin = 13;
const int wifiSignalLED = 2;
const int portalSignalLED = 4;

char incoming;

using namespace ace_button;

// Number of buttons and LEDs.
const uint8_t NUM_LEDS = 6;

uint8_t LED1 = 5;
uint8_t LED2 = 18;
uint8_t LED3 = 19;
uint8_t LED4 = 21;
uint8_t LED5 = 22;
uint8_t LED6 = 23;

struct Info {
  const uint8_t buttonPin;
  const uint8_t ledPin;
  // bool ledState;
};

Info INFOS[NUM_LEDS] = {
  {12, LED1},
  {14, LED2},
  {27, LED3},
  {26, LED4},
  {25, LED5},
  {33, LED6},
};

AceButton buttons[NUM_LEDS];

void handleWifiConnection (void * parameter){

  wifiManager.setConfigPortalTimeout(1);
  if (!wifiManager.autoConnect("AutoConnectAP", "123456789")) {
    Serial.println("remmembering states");
    delay(300);
  }
      
  for(;;){
    if (WiFi.status() == WL_CONNECTED){
      notConnected = false;
      // Serial.println("wifi still connected");

      digitalWrite(wifiSignalLED, HIGH);

      
      if (client.connect("www.google.com", 80)) Serial.println("has internet acces");
      else Serial.println("has no internet access");
      client.stop();

    }else {
      notConnected = true;
      // Serial.println("wifi not connected!!");
      digitalWrite(wifiSignalLED, LOW);

      WiFi.begin(wifiManager.getWiFiSSID().c_str(), wifiManager.getWiFiPass().c_str());
    }

    if(!digitalRead(portalTrigerPin)){
      vTaskDelay(1000 / portTICK_PERIOD_MS);
      if(!digitalRead(portalTrigerPin)){
        digitalWrite(portalSignalLED, HIGH);

          wifiManager.setConfigPortalTimeout(120);
        if (!wifiManager.startConfigPortal("AutoConnectAP", "123456789")) {
            Serial.println("failed to connect and hit timeout");
          }
          Serial.println("\tmqtt_server : " + String(deviceId.getValue()));
          digitalWrite(portalSignalLED, LOW);
        }
    }
    vTaskDelay(1000 / portTICK_PERIOD_MS);
  }
}

void handleNearbyCommunication(void * parameters){
  for(;;){
      if(Serial2.available()){
        incoming = Serial2.read();
        Serial2.write(incoming);

        switch (incoming){
          // ON #fff
          case 'A':
            digitalWrite(LED1, HIGH);
            break;
          case 'B':
            digitalWrite(LED2, HIGH);
            break;
          case 'C':
            digitalWrite(LED3, HIGH);
            break;
          case 'D':
            digitalWrite(LED4, HIGH);
            break;
          case 'E':
            digitalWrite(LED5, HIGH);
            break;
          case 'F':
            digitalWrite(LED6, HIGH);
            break;
          case 'Z':
            digitalWrite(LED1, HIGH);
            digitalWrite(LED2, HIGH);
            digitalWrite(LED3, HIGH);
            digitalWrite(LED4, HIGH);
            digitalWrite(LED5, HIGH);
            digitalWrite(LED6, HIGH);
            break;

            // OFF #FFF
          case 'a':
            digitalWrite(LED1, LOW);
            break;
          case 'b':
            digitalWrite(LED2, LOW);
            break;
          case 'c':
            digitalWrite(LED3, LOW);
            break;
          case 'd':
            digitalWrite(LED4, LOW);
            break;
          case 'e':
            digitalWrite(LED5, LOW);
            break;
          case 'f':
            digitalWrite(LED6, LOW);
            break;
          case 'z':
            digitalWrite(LED1, LOW);
            digitalWrite(LED2, LOW);
            digitalWrite(LED3, LOW);
            digitalWrite(LED4, LOW);
            digitalWrite(LED5, LOW);
            digitalWrite(LED6, LOW);
            break;
        }
    }
    // vTaskDelay(100 / portTICK_PERIOD_MS);
      for (uint8_t i = 0; i < NUM_LEDS; i++) {
    buttons[i].check();
  }
  }
}


// Forward reference to prevent Arduino compiler becoming confused.
void handleEvent(AceButton*, uint8_t, uint8_t);

void setup() {
    Serial.begin(115200);
    Serial2.begin(9600);
    // Serial2.println("seial2 started");

    
  wifiManager.addParameter(&deviceId);
 
    irrecv.enableIRIn();

  // signals #fff
    pinMode(wifiSignalLED, OUTPUT);
    pinMode(portalSignalLED, OUTPUT);
    pinMode(portalTrigerPin, INPUT_PULLUP);

    // buttons #fff

      for (uint8_t i = 0; i < NUM_LEDS; i++) {
      // initialize built-in LED as an output
      pinMode(INFOS[i].ledPin, OUTPUT);

      // Button uses the built-in pull up register.
      pinMode(INFOS[i].buttonPin, INPUT_PULLUP);

      // initialize the corresponding AceButton
      buttons[i].init(INFOS[i].buttonPin, HIGH, i);
    }

    // Configure the ButtonConfig with the event handler, and enable all higher
    // level events.
    ButtonConfig* buttonConfig = ButtonConfig::getSystemButtonConfig();
    buttonConfig->setEventHandler(handleEvent);
    buttonConfig->setFeature(ButtonConfig::kFeatureClick);

    xTaskCreate(
      handleWifiConnection,
      "handleWifiConnection",
      3096,
      NULL,
      1,
      NULL
    );

    xTaskCreate(
      handleNearbyCommunication,
      "handleNearbyCommunication",
      1024,
      NULL,
      1,
      NULL
    );
}

void loop(){
  if (irrecv.decode(&results)) {
    switch (results.value) {
      case 0xD671807F:
        digitalWrite(LED1, !digitalRead(LED1));
        break;

      case 0xD67140BF:
        digitalWrite(LED2, !digitalRead(LED2));
        break;

      case 0xD671C03F:
        digitalWrite(LED3, !digitalRead(LED3));
        break;

      case 0xD67120DF:
        digitalWrite(LED4, !digitalRead(LED4));
        break;

      case 0xD671A05F:
        digitalWrite(LED5, !digitalRead(LED5));
        break;

      case 0xD671609F:
        digitalWrite(LED6, !digitalRead(LED6));
        break;
    }

    irrecv.resume();
  }
  delay(100);
}

// The event handler for the button.
void handleEvent(AceButton* button, uint8_t eventType, uint8_t buttonState) {

  // Get the LED pin
  uint8_t id = button->getId();
  uint8_t ledPin = INFOS[id].ledPin;

  if (eventType == AceButton::kEventPressed) digitalWrite(ledPin, !digitalRead(ledPin));
}