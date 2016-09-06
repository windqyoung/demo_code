

require 'net/http'

uri = URI('http://www.stats.gov.cn/tjsj/tjbz/xzqhdm/201504/t20150415_712722.html')

str = Net::HTTP.get(uri).force_encoding('utf-8')

reg = Regexp.new <<'REG'.strip
<p class="MsoNormal".+?>(\d+)<.*?font-size: 12pt">(.+?)</span></p>
REG

h = {}
str.scan reg do |x, y|
  h[x] = y.sub(/^　*/, '')
end

cs = []
h.sort.each do |x, y|
  if x[-4..-1] == '0000'
    # 第一级
    cs.push({
        id: x,
        name: y,
        children: [],
    })
  elsif x[-2..-1] == '00' # 二级
    cs.last[:children].push({
       id: x,
       name: y,
       children: [],
    })
  else
    cs.last[:children].last[:children].push({
        id: x,
        name: y,
    })
  end
end

cs.each do |x|
  printf("%s, %s\n", x[:id], x[:name])

  x[:children].each do |x|
    printf("    %s, %s\n", x[:id], x[:name])
    x[:children].each do |x|
      printf("        %s, %s\n", x[:id], x[:name])
    end
  end
end

# require 'json'
# fileName = 'cities.level.json'
# JSON.dump(cs, File.open(fileName, 'w'))
