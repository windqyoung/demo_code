import groovy.json.JsonOutput
import groovy.json.JsonSlurper

/**
 * Created by windq on 2015/12/16 0016.
 */


url = 'http://www.stats.gov.cn/tjsj/tjbz/xzqhdm/201504/t20150415_712722.html'.toURL()

html = url.text
reg = ~$/<p class="MsoNormal".+?>(\d+)<.*?font-size: 12pt">(.+?)</span></p>/$
match = html =~ reg

cts = [:]
match.each {
    cts[it[1]] = it[2].replaceAll(/　/, '')
}

cs = []
cts.sort().each { k, v ->
    if (k[-4..-1] == '0000') {
        cs << [
            id: k,
            name: v,
            children: []
        ]
    } else if (k[-2..-1] == '00') {
        cs.last()['children'] << [
            id: k,
            name: v,
            children: []
        ]
    } else {
        cs.last()['children'].last()['children'] << [
            id: k,
            name: v,
        ]
    }
}

cs.each {
    println "$it.id: $it.name"
    it.children.each {
        println "    $it.id: $it.name"
        it.children.each {
            println "        $it.id: $it.name"
        }
    }
}



//new File('cities.txt').write(JsonOutput.prettyPrint(JsonOutput.toJson(cs)))
