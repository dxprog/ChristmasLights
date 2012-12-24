import serial
import json
import datetime
import pygame

def writeNumber(value):
	s.write(str(unichr(value)))

def playSequence(name):

	f = open(name + '.js')
	sequence = ''
	for line in f:
			sequence += line

	sequence = json.loads(sequence)

	pygame.mixer.music.load(name + '.ogg')

	ticks = 0
	item = 0

	pygame.mixer.music.play()

	while pygame.mixer.music.get_busy():
			
			if s.inWaiting() > 0:
				pygame.mixer.music.stop()
			
			if item < len(sequence) and pygame.mixer.music.get_pos() >= sequence[item]['timestamp'] + 1400:
					for action in sequence[item]['actions']:
							value = (int(action['stringId']) & 0xf) | (action['value'] & 0xf0)
							writeNumber(value)
							print 'Set string %d to %d : (%d)' % (action['stringId'], action['value'], value)
					item += 1

s = serial.Serial(port='/dev/ttyACM0', baudrate=115200)
mode = 0
dataSent = False

pygame.init()

while True:

	if s.inWaiting() > 0:
		mode += 1
		mode = 0 if mode > 2 else mode
		s.read()
		dataSent = False
	
	if mode == 0:
		playSequence('holy_night')
	
	elif mode == 1 and dataSent == False:
		writeNumber(97)
		writeNumber(98)
		writeNumber(99)
		writeNumber(100)
		dataSent = True
		
	elif mode == 2 and dataSent == False:
		writeNumber(1)
		writeNumber(2)
		writeNumber(3)
		writeNumber(4)
		dataSent = True

s.close()
