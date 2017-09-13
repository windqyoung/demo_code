

def b = new ProcessBuilder('php-cgi')
def env = b.environment()
env.REQUEST_METHOD = 'POST'
env.SCRIPT_FILENAME = new File('scriptfilename.php').absolutePath
env.REDIRECT_STATUS = "true"
env.CONTENT_TYPE = 'application/x-www-form-urlencoded'
env.QUERY_STRING = 'Q=1&U=2'

def post = 'a=b&c=d'
env.CONTENT_LENGTH = post.bytes.length.toString()

def inf = new File('infile')
inf.write(post)

b.redirectInput(inf)

def p = b.start()
println p.inputStream.text