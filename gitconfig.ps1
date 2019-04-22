git config --global --add alias.dfwbl "diff --ignore-blank-lines  -w"
git config --global --add alias.co "checkout"
git config --global --add alias.dfbl "diff --ignore-blank-lines"
git config --global --add alias.rp "rev-parse"
git config --global --add alias.ci "commit -v"
git config --global --add alias.log1 "log --oneline"
git config --global --add alias.lg "log --color --graph --pretty=format:'%Cred%h%Creset -%C(yellow)%d%Creset %s %Cgreen(%cI) %C(bold blue)<%an>%Creset' --abbrev-commit"
git config --global --add alias.su "submodule"
git config --global --add alias.st "status"
git config --global --add alias.dh "diff HEAD"
git config --global --add alias.desc "describe"
git config --global --add alias.sm "submodule"
git config --global --add alias.df "diff"
git config --global --add alias.diffc "diff --cached"
git config --global --add alias.br "branch -vv"
git config --global --add alias.dc "diff --cached"
git config --global --add alias.bi "bisect"
git config --global --add alias.dfw "diff -w"
git config --global --add alias.ns "log --name-status --color"
git config --global --add alias.rt "remote -v"

git config --global --add core.filemode false
git config --global --add core.autocrlf "input"
git config --global --add core.symlinks "false"

git config --global tag.sort version:refname
git config --global --add alias.tv  "tag --sort=version:refname"
git config --global --add alias.tv- "tag --sort=-version:refname"

git config --global log.date iso-strict


