# -*- mode: ruby -*-
# vi: set ft=ruby :
Vagrant.configure(2) do |config|

    config.vm.define :default do |config|
      config.vm.box = "ubuntu/trusty64"
      config.vm.synced_folder ".", "/vagrant", type: :nfs
      config.vm.provision "shell", inline: "touch /home/vagrant/.linux && chown vagrant:vagrant /home/vagrant/.linux"
      config.vm.provision "shell", path: "vagrant/provisioning.sh"
    end

    config.vm.usable_port_range = (2200..2299)
    config.vm.network "forwarded_port", guest: 80, host: 2280, auto_correct: true
    config.vm.network "forwarded_port", guest: 3306, host: 2206, auto_correct: true
    config.vm.network "private_network", ip: "192.168.1.10"

    config.ssh.forward_agent = true

    config.vm.provider "virtualbox" do |vbox|
        vbox.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
        vbox.customize ["setextradata", :id, "VBoxInternal2/SharedFoldersEnableSymlinksCreate/v-root", "1"]
        vbox.memory = "1024"
        vbox.cpus = 2
    end
end
