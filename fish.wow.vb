MourseShapeValue = 1053591638

p_left = 647  
p_top = 351   
p_right = 1175  
p_bottom = 541  

p_width = 10
p_height = 10


check_color_step = 100

Call Plugin.Media.Beep(523, 400)


TracePrint "移动到声音位置"

def_music_x = 1912
def_music_y = 250

MoveTo def_music_x, def_music_y

WaitKey
Call Plugin.Media.Beep(578, 400)
GetCursorPos music_x, music_y



MusicColor = GetPixelColor(music_x, music_y)


TracePrint "取位置颜色: " & music_x & "," & music_y & "," & MusicColor

While True

	Rem loop_start

	MoveTo p_left, p_top

	Delay 2000
	
	Call Plugin.Media.Beep(659, 400)

	
	KeyPress "1", 1
	
	Delay 1000

	

	For y = p_top To p_bottom Step p_height
		For x = p_left To p_right Step p_width
		
			MoveTo x, y
			Delay 10
			
			m_shape = GetCursorShape(0)
			
			If m_shape = MourseShapeValue Then 
			
				Call Plugin.Media.Beep(698, 400)

				
				// 找到光标了
				MoveTo x + 20, y + 20
				
				For wait_ms = 0 To 20000 step check_color_step
				
				
					m_shape = GetCursorShape(0)
			
					If m_shape = MourseShapeValue Then 
					
					Else 
						// 鱼漂丢了
						Call Plugin.Media.Beep(784, 400)

						Goto loop_start
					End if
					
			
					IfColor music_x, music_y, MusicColor, 1 Then
						// 声音检测到了
						Call Plugin.Media.Beep(880, 400)

						
						RightClick 1
						
						Delay 1000
						Goto loop_start
						
					End If
					
					Delay check_color_step

				Next

				// 鱼漂时间用光了
				Goto loop_start

				
			End If

		Next
	Next
	

Wend

