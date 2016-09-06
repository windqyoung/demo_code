import groovy.io.FileType

import java.nio.file.Files
import java.nio.file.StandardCopyOption

if (args.size() < 2)
{
    println "usage: src desc [--copy]"
    return
}

srcPath = args[0]
descPath = args[1]
copyToDesc = args.contains('--copy')

srcFile = new File(srcPath)
descFile = new File(descPath)

if (! srcFile.exists() || ! descFile.exists())
{
    println "dir not exists, please check"
    return
}

println """\
checking: ${srcFile.canonicalPath}
               vvvvvvvvv
          ${descFile.canonicalPath}
"""

newFile = []
changedFile = []
sameFile = []
ignoreFile = []

// 忽略的地址
def ignore(File f)
{
    def regs = [
        $/[\\/]\./$,
        'Command',
        'vendor',
        'storage',
        $/public[\\/]image[\\/]item/$,
        /TestController\.php/,
        /diff_file_tree\.groovy/,
    ]
    return regs.any({
        f.canonicalPath =~ it
    })
}

def targetFile(File f)
{
    def relPath = f.canonicalPath - srcFile.canonicalPath
    // 目标地址
    new File(descFile, relPath)
}

def isTextFile(File f)
{
    def exts = [
        '.php',
        '.js',
        '.css',
    ]

    exts.any({
        f.name.endsWith(it)
    })
}

def showFileName(File f)
{
    f.canonicalPath - srcFile.canonicalPath
}

def showAndCopyFile(File f)
{
    print showFileName(f)
    if (copyToDesc)
    {
        print "\t\tcopying..."
        copyFile(f)
        print 'done'
    }

    println()
}

def canCopy(File f)
{
    def cannot = [
//        new File(srcFile, 'config/app.php'),
    ]

    ! cannot.any {
        Files.isSameFile(f.toPath(), it.toPath())
    }
}

def copyFile(File f)
{
    if (! canCopy(f))
    {
        print ' cannot copy, skip '
        return
    }
    def target = targetFile(f)
    def parent = target.parentFile
    if (! parent.exists())
    {
        print ' create new dir '
        Files.createDirectories(parent.toPath()) // 如果是新目录, 创建
    }
    Files.copy(f.toPath(), target.toPath(), StandardCopyOption.REPLACE_EXISTING)
}

def isNewFile(File f)
{
    def target = targetFile(f)
    // 目标不存在 , 说明是新文件
    ! target.exists()
}

def isContentSame(File f)
{
    def target = targetFile(f)
    // 文本文件
    if (isTextFile(f))
    {
        // 文件相同, 文本文件比较行
        return f.readLines() == target.readLines()
    } else
    {
        // 二进制, 比较字节
        return f.bytes == target.bytes
    }
}

srcFile.eachFileRecurse(FileType.FILES) { f ->
    if (ignore(f))
    {
        ignoreFile << f
        return
    }

    // 新文件
    if (isNewFile(f))
    {
        newFile << f
        return
    }

    // 内容相同
    if (isContentSame(f))
    {
        sameFile << f
    } else
    {
        changedFile << f
    }
}

println '============new files=================='
newFile.each {
    showAndCopyFile(it)
}

println '============changed files=============='
changedFile.each {
    showAndCopyFile(it)
}

println()
changedFile.each {
    if (!canCopy(it)) {
        println "${showFileName(it)} cannt copy, please change by yourself"
    }
}
