int
  rxValue = 0,
  rxPin = 0,
  rxState = 0,
  btnState = 0,
  btnPrevState = 0,
  lightMode = 0,
  allOn = 0;
  
void setup() {
  Serial.begin(115200); 
  pinMode(4, OUTPUT);
  pinMode(5, OUTPUT);
  pinMode(6, OUTPUT);
  pinMode(7, OUTPUT);
  pinMode(13, INPUT);
}

void setAll(int value) {
  for (int i = 0; i < 4; i++) {
    digitalWrite(i + 3, value);
  }
}

void loop() {
  
  btnState = digitalRead(13);
  if (btnState != btnPrevState && btnPrevState == 1) {
    Serial.write(1);
  }
  btnPrevState = btnState;
  
  if (Serial.available() > 0) {
    rxValue = Serial.read();
    rxPin = rxValue & 0xf;
    rxState = rxValue & 0xf0;
    if (rxState > 0) {
      digitalWrite(rxPin + 3, LOW);
    } else {
      digitalWrite(rxPin + 3, HIGH);
    }
  }
}