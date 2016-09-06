import javax.swing.JButton
import javax.swing.JFrame
import javax.swing.JLabel
import javax.swing.JPanel
import javax.swing.JTextField
import javax.swing.SwingUtilities
import java.awt.Color
import java.awt.GridLayout

jf = new JFrame()

northPanel = new JPanel()
jf.add(northPanel, 'North')

northPanel.add(new JLabel('rows'))
yf = new JTextField('50', 10)
northPanel.add(yf)

northPanel.add(new JLabel('cols'))
xf = new JTextField('50', 10)
northPanel.add(xf)

northPanel.add(new JLabel('sleep'))
sf = new JTextField('10', 10)
northPanel.add(sf)

startBtn = new JButton('start')
northPanel.add(startBtn)

stopBtn = new JButton('stop')
northPanel.add(stopBtn)

view = new JPanel()
jf.add(view)

jf.setSize(800, 600)
jf.setDefaultCloseOperation(3)
jf.setVisible(true)

viewMap = []
model = []
running = false

stopBtn.addActionListener {
    running = false
}

startBtn.addActionListener {
    if (running) {
        println 'is running'
        return
    }
    running = true
    Thread.start {
        initView()
        initModel()
        while (running) {
            remodel()
            SwingUtilities.invokeLater {
                repaint()
            }
            sleep(sf.getText() as int)
        }
    }
}

def rows()
{
    yf.text as int
}

def cols()
{
    xf.text as int
}

def index(x, y)
{
    y * (cols()+2) + x
}


def initModel() {
    Arrays.fill(model = new boolean[(rows() + 2) * (cols() + 2)], false)
    def r = new Random()
    1.upto(rows()) { y ->
        1.upto(cols()) { x ->
            model[index(x, y)] = r.nextBoolean()
        }
    }
}

def initView()
{
    view.removeAll()
    view.setLayout(new GridLayout(rows(), cols()))
    1.upto(rows()) { y ->
        1.upto(cols()) { x ->
            def n = index(x, y)
            def jb = viewMap[n] = new JButton("$y:$x")
            view.add(jb)
            jb.putClientProperty('nb', n)
            jb.addActionListener {
                model[n] = !model[n]
            }
        }
    }
    view.validate()
}


def repaint()
{
    1.upto(rows()) { y ->
        1.upto(cols()) { x ->
            def n = index(x, y)
            if (model[n]) {
                viewMap[n].setBackground(Color.GRAY)
            } else
            {
                viewMap[n].setBackground(Color.WHITE)
            }
        }
    }
}
def remodel()
{
    def old = model.clone()

    1.upto(rows()) {y ->
        1.upto(cols()) {x ->
            model[index(x, y)] = alive(x, y, old)
        }
    }
}


def alive(x, y, old)
{
    def sum = 0
    (y-1).upto(y+1) { yy ->
        (x-1).upto(x+1) { xx ->
            sum += old[index(xx, yy)] ? 1 : 0
        }
    }
    sum -= old[index(x, y)] ? 1 : 0

    3 == sum || (2 == sum && old[index(x, y)])
}
