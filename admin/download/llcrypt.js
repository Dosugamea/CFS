var signKey = {
  JP: 'Hello',
  EN: 'BFd3EnkcKa',
  TW: 'M2o2B7i3M6o6N88',
  CN: 'iLbs0LpvJrXm3zjdhAr4'
}

var keyrotate = {
  JP: new Buffer('29002348be188467e14a6c3dd62cae725269905f4916f16df15abb41e926eb01b30ba62edb123c15877e0c393e0f990024015e300d441c49064db74d4715de54b339122d4d07c84d4364bb668b42a6261f70035d5a7a7d7609453812253b1f1e5d6ed41acb63fc6b967ff57f454e3b3213220d26896b0a031c30db0bae56320720019a755023ee22404b7858366bfd5c123e491a325ff63b9e3a7d79495fdc0dad4c4f31145ef24d4449402e6613d01c6b36c4663042b77e32603b2ca1152254f63e220891599d40e1128b791f12da73b058ca2699360209b97b72579d1349702c69804a7e18c5169968d53ce9138040b25dea33c923cc485357bf60675cd63c', 'hex'),
  EN: new Buffer('16A990AAA3C1F5D7BFD20AE1C5B9D3C35559CB8368667DB74CFDB31BA315962A5E74381A071F18198435AAB2C7539825452CBD589308904F2138F4BAFDD77E2F09E4439BFF838DF003605729CD181444041C0A459169DF216F96DFD7224F46D9848AE0F5976A4FC01DFD36B8350C3544A41D70342FF6CDAEDF913B72003C2EA4D837C25864DEE8D3A5844C8C30904964C1AEE42508B9F4CDED52BD1F09FBC5F46651177C5CA98608B2C4369D64E900449F8ED0CA11E6F1136111BB6FCFD87A33123D76625CD382483A87C5A2405CBE5830209BA59B17DEECAA1D7E5F01F87EAB1348D253FD8AF45580DEE9821DA3EF9A2FA2A2B07E03B0C5EBDA3C13A3C280D2','hex'),
  TW: new Buffer('18E525A9A4C4B95A580595018271FFAC313318E863691B9D159D8E0BBBD0DA96351E940F63E368C9AAA6582002BB767103244B4A234E7AEDE61EB43B064C6371DAD10D7EC9253334F6427BE98F3C8FF6D8DE87159B5F93099B3073327831BCEFDD1BC094BBA3CE408A5C78D58EB9C10EB6D2D2C8B1777DEFAF4A8171AB8E832E587F186B4E92BCA9A65BAB6E2F6D8F73A49AB4C1535D6AAB28F758F95BDB0C5A313913B8C3363392E01DA4B5339B815FAF763A1F7C7AFB566771AE64F2009CF3C4616F8FB9B9796AA6B10A5B0A7AF0B7FF3530224C66A81A16DB3E55C63092378AEBAEA2EAD047F6F6B21CA917F870BB8135D694D6FAA74915DDED7BED3C91C6', 'hex'),
  CN: new Buffer('5856691b13a2430a6308ad0e6d0500141d4670d4002315b6bc54e0fb12f1c99ab6cad32328e08fcd74bd056912a6a30179a5966ed17a3d33ff8b68b695041629cf3b74d7bb97de8e8d7ecbca231cd8244769fcdbc82175b0aee206f52fdf483f72b1be52e8355969a9a0e21309f4ede2c1a5cb9690e8b1db68f92a4cc617fd17a8f59a1b25bcc09779c81384e13fb1d948a966403a026296eefea474f6b4241fc888766370cf7c7aec2e04912cd0ed57dda26d66e99d8392eda9ba432c8e4a02727beed4728bc134c475b2136e6a50edb9291cbc20a266fae34d36c2b2527e763924d3e2f5cef0e687868cd195a2bb145bd184cd820f29a0fc5ae9d3b4976a9c', 'hex')
}

var lngTbl = {
  JP: new Buffer('41C64E6D000030390000000F015A4E350000000100000017000343FD00269EC300000018000101010041592700000008', 'hex')
}

var fs = require('fs')
var path = require('path')
var crypto = require('crypto')

var cluster = require('cluster')
if (cluster.isMaster) {
  main()
} else {
  process.on('message', workerMain)
}

function doV1(buf, fnameLength, md5) {
	var pos = 0
	fnameLength	= (fnameLength & 0x3F) + 1
	var key = new Buffer(4)
	md5.slice(0, 4).copy(key)
	for(i = 0; i < buf.length; i++) {
		buf[i] = buf[i] ^ (key.readUInt8(pos))
		pos = (pos + 1) % 4
		if (!pos) {
			var _key = key.readUInt32LE(0)
			_key = ((((fnameLength + ((((_key << 16) | _key & 0xFF00) << 8) | (((_key >>> 16) | _key & 0xFF0000) >>> 8))) << 16) | (((fnameLength + (((_key & 0xFF00) << 8) | (((_key >>> 16) | _key & 0xFF0000) >>> 8))) & 0xFF00) & 0xFFFF)) << 8) | ((((fnameLength + ((((_key << 16) | _key & 0xFF00) << 8) | (((_key >>> 16) | _key & 0xFF0000) >>> 8))) >>> 16) | (fnameLength + ((((_key << 16) | _key & 0xFF00) << 8) | (((_key >>> 16) | _key & 0xFF0000) >>> 8))) & 0xFF0000) >>> 8)
			if (_key < 0) _key = 0x100000000 + _key
			key.writeUInt32LE(_key, 0)
		}
	}
}

function doV2(from, to, md5) {
	var key = md5[3] | ((md5[2] | ((md5[1] | ((md5[0] & 0x7F) << 8)) << 8)) << 8)
	var keysplit = [key >>> 23, (key >>> 15) & 0xFF]
	var ofs2 = 0
	for(i = 0; i < from.length; i++) {
		to[i] = from[i] ^ keysplit[ofs2]
		if (ofs2 == 1) {
			var newkey = ((0x0041A7 * (key >>> 16)) >>> 15) + (0xC1A70000 * (key >>> 16) & 0x7FFF0000) + 0x0041A7 * (key & 0x00FFFF)
			if (newkey > 0x7FFFFFFE) {
				newkey -= 0x7FFFFFFF
			}
			key = newkey
			keysplit = [key >>> 23, (key >>> 15) & 0xFF]
		}
		ofs2 ^= 1
	}
}

function doV3(from, to, key) {
	for(i = 0; i < from.length; i++) {
		to[i] = from[i] ^ (key >>> 24)
		key = (key * 0x0343FD + 0x269EC3) & 0xFFFFFFFF
	}
}

function multiply_uint32(a, b) {
    var ah = (a >> 16) & 0xffff, al = a & 0xffff;
    var bh = (b >> 16) & 0xffff, bl = b & 0xffff;
    var high = ((ah * bl) + (al * bh)) & 0xffff;
    return ((high << 16)>>>0) + (al * bl);
}

function doV4(from, to, key, idx) {
	for(i = 0; i < from.length; i++) {
		to[i] = from[i] ^ (key >>> lngTbl.readUInt32BE(idx * 12 + 8))
		key = ((multiply_uint32(key, lngTbl.readUInt32BE(idx * 12)) & 0xFFFFFFFF) + lngTbl.readUInt32BE(idx * 12 + 4)) & 0xFFFFFFFF
	}
}

function getHeaderMD5(fname) {
	var name = signKey + path.basename(fname)
	var md5 = crypto.createHash('md5')
	md5.update(name)
	return new Buffer(md5.digest('base64'), 'base64')
}

function detectType(fname, content) {
	var md5 = getHeaderMD5(fname)
	if (content.slice(0, 4).toString('hex') == md5.slice(4, 8).toString('hex')) {
		return [2, md5]
	}
	var _md5 = new Buffer(3)
	md5.slice(4, 7).copy(_md5) 
	for (var i = 0; i < 3; i++) {
		_md5[i] = 0xFF - _md5[i]
	}
	if (content.slice(0, 3).toString('hex') == _md5.toString('hex')) {
		return [3, md5]
	}
	return [-1, md5]
}

function decrypt(fname, content, id) {
	var type = detectType(fname, content)
	if (type[0] == -1) {
    return console.log('[Worker ' + id + '][SKIP]%s: Not encrypted.', fname)
	} else if (type[0] == 2) {
		var headerLength = 4
		var md5 = type[1]
		var output = new Buffer(content.length - headerLength)
		console.log('[Worker ' + id + '][DEC-V2]%s', fname)
		doV2(content.slice(headerLength), output, md5)
	} else if (type[0] == 3) {
		var headerLength = 4 + content[3]
    if (content[7] == 0) {
      var keyoffset = (content.readUInt32LE(8) >>> 0x18) & 0x3F
      var key = keyrotate.readUInt32LE(keyoffset * 4)
      var output = new Buffer(content.length - headerLength)
      console.log('[Worker ' + id + '][DEC-V3]%s', fname)
      doV3(content.slice(headerLength), output, key)
    } else if (content[7] == 2) {
      if (!lngTbl) {
        return console.log('[Worker ' + id + ']ERROR: lngTbl not found for V4, file ' + fname)
      }
      var idx = content[6] & 3
      var key = type[1].readUInt32BE(8)
      var output = new Buffer(content.length - headerLength)
      console.log('[Worker ' + id + '][DEC-V4]%s', fname)
      doV4(content.slice(headerLength), output, key, idx)
    } else {
      return console.log('[Worker ' + id + ']ERROR: not supported encrypt V' + (content[7] + 2) + ', file ' + fname)
    }
	}
	fs.writeFileSync(fname, output)
}

function encrypt(fname, content, encType, id) {
  if (content.length === 0) {
    return console.log('[Worker ' + id + '][SKIP]%s: Empty file.', fname)
  }
	var type = detectType(fname, content)
	if (type[0] !== -1) {
		return console.log('[Worker ' + id + '][SKIP]%s: Already encrypted, Type %d.', fname, type[0])
	}
	var header
	if (encType == 3) {
		header = new Buffer(16)
		type[1].slice(4, 7).copy(header)
		for (var i = 0; i < 3; i++) {
			header[i] = 0xFF - header[i]
		}
		header[3] = 12
		header.fill(0, 4, 16)
		var output = new Buffer(content.length)
		console.log('[Worker ' + id + '][ENC-V3]%s', fname)
		doV3(content, output, keyrotate.readUInt32LE(0))
	} else if (encType == 2) {
		header = type[1].slice(4, 8)
		var output = new Buffer(content.length)
		console.log('[Worker ' + id + '][ENC-V2]%s', fname)
		doV2(content, output, type[1])
	}
	fs.writeFileSync(fname, Buffer.concat([header, output]))
}

function cryptV1(fname, content, id) {
	var md5 = detectType(fname, content)[1]
	console.log('[Worker ' + id + '][CRYPT-V1]%s', fname)
	doV1(content, path.basename(fname).length, md5.slice(0, 4))
	fs.writeFileSync(fname, content)
}

function main() {
  console.log('======================================')
  console.log('| Programmed Live! LLCrypt ver 0.6.0 |')
  console.log('|                 by NijiharaTsubasa |')
  console.log('|                                    |')
  console.log('|      DO NOT REDISTRIBUTE!!!!!      |')
  console.log('======================================')

  var modeIsEncrypt, encryptType = 3
  var filein = []
  var invalid = false, parameters_end = false, includeFont = false, includeVideo = false
  var useKey = 'JP'
  if (process.argv.length < 3) {
    invalid = true
    console.log('Error: No command given!')
  }
  for (var i = 2; i < process.argv.length; i++) {
    if (i == 2) {
      if (process.argv[i].substr(0, 2) == '-e') {
        modeIsEncrypt = true
        if (process.argv[i] == '-e2') {
          encryptType = 2
        }
      } else if (process.argv[i] == '-d') {
        modeIsEncrypt = false
      } else if (process.argv[i] == '-1') {
        encryptType = 1
      } else {
        invalid = true
        console.log('Error: Unknown command!')
        break
      }
    } else {
      if (!parameters_end && process.argv[i][0] == '-') {
        switch (process.argv[i][1]) {
          case '-': {
            if (process.argv[i] == '--TW') {
              useKey = 'TW'
            } else if (process.argv[i] == '--CN') {
              useKey = 'CN'
            } else if (process.argv[i] == '--EN') {
              useKey = 'EN'
            } else if (process.argv[i] == '--include-font') {
              includeFont = true
            } else if (process.argv[i] == '--include-video') {
              includeVideo = true
            }
            break
          }
          default: console.log('Error: unknown option ' + process.argv[i]);invalid = true;break;
        }
        if (invalid) break
      } else {
        parameters_end = true
        filein.push(process.argv[i])
      }
    }
  }

  if (!invalid && !filein.length) {
    console.log('Error: Input file not specified!')
    invalid = true
  }

  if (invalid) {
    console.info('Usage:')
    console.info('node ' + process.argv[1].substr(process.argv[1].replace(/\\/g, '/').lastIndexOf('/') + 1) + ' -d|-e|-e2|-1 [options] filename')
    console.info('-d		Decrypt a file or all files in a folder that was encrypted\n		with V2 or V3 or V4 encryption (Will be auto detected)')
    console.info('-e		Encrypt a file or all files in a folder using V3 encryption.')
    console.info('-e2		Encrypt a file or all files in a folder using V2 encryption.')
    console.info('-1		Decrypt / Encrypt a file or all files in a folder with V1 encryption.')
    console.info('		WARNING -1 will NOT detect whether the file is encrypted or not!!!')
    console.info('\nOptions:')
    console.info('--include-font	Do not exclude *.ttf when encrypting.\n		This is most likely NOT what you want since font files should always\n		stay decrypted in game data.')
    console.info('--include-video	Do not exclude *.mp4 when encrypting.\n		This is most likely NOT what you want since video files should always\n		stay decrypted in game data.')
    console.info('--TW		Encrypt / Decrypt as LoveLive TW format.')
    console.info('--CN		Encrypt / Decrypt as LoveLive CN format.')
    console.info('--EN		Encrypt / Decrypt as LoveLive EN format.')
    console.info('\nNote that the key customization option is no longer supported.\nIf you need to customize the key, please modify the "signKey" and "keyrotate" in the script.')
    return
  }

  console.log('Use key:' + signKey[useKey])
  
  var realFilein = []
  addToQueue(realFilein, filein, modeIsEncrypt, includeFont, includeVideo)
  
  if (!realFilein.length) {
    return
  } else if (realFilein.length == 1) {
    workerMain({files: [realFilein[0]], useKey: useKey, isEnc: modeIsEncrypt, type: encryptType, id: 0})
  } else {
    var workerCnt = Math.min(realFilein.length, require('os').cpus().length)
    var realWork = new Array(workerCnt)
    while (realFilein.length) {
      for (var i = 0; i < workerCnt && realFilein.length; i++) {
        if (!realWork[i]) {
          realWork[i] = []
        }
        realWork[i].push(realFilein.shift())
      }
    }
    realWork = realWork.map(function (e) {
      return {files: e, useKey: useKey, isEnc: modeIsEncrypt, type: encryptType}
    })
    var workers = []
    for (var i = 0; i < workerCnt; i++) {
      workers.push(cluster.fork())
    }
    startDistributeWork(realWork, workers)
  }
}

var _dirlog = false
function addToQueue(queue, filelist, modeIsEncrypt, includeFont, includeVideo) {
  while (filelist.length) {
    var filein = filelist.shift()
    try {
      if (fs.lstatSync(filein).isDirectory()) {
        if (!_dirlog) {
          console.log('Listing all files from input folder, please wait...')
          _dirlog = true
        }
        addToQueue(queue, fs.readdirSync(filein).map(function (file) {
          return filein + '/' + file
        }), modeIsEncrypt, includeFont, includeVideo)
      } else {
        if (modeIsEncrypt) {
          if (!includeFont && path.extname(filein) === '.ttf') {
            console.log('[SKIP]%s: is FONT file.', filein)
            continue
          }
          if (!includeVideo && path.extname(filein) === '.mp4') {
            console.log('[SKIP]%s: is VIDEO file.', filein)
            continue
          }
        }
        queue.push(filein)
      }
    } catch (e) {
      console.log('[ERROR]%s: File not found', filein)
    }
  }
}

function startDistributeWork(workList, workers) {
  workers.forEach(function (e, i) {
    e.on('online', function () {
      console.log('Worker ' + i + ' started!')
      workList[i].id = i
      e.send(workList[i])
    })
    e.on('message', function () {
      this.kill()
    })
    e.on('exit', function (code) {
      if (!code) {
        return console.log('Worker ' + i + ' terminated with success.')
      }
      console.log('Worker ' + i + ' crashed... Unfinished work on that worker will be lost.')
    })
  })
}

function workerMain(work) {
  if (lngTbl.JP) {
    lngTbl = lngTbl[work.useKey]
    keyrotate = keyrotate[work.useKey]
    signKey = signKey[work.useKey]
  }
  var files = work.files, modeIsEncrypt = work.isEnc, encryptType = work.type
  files.forEach(function (file) {
    var content = fs.readFileSync(file)
    if (encryptType == 1) {
      cryptV1(file, content, work.id)
    } else if (modeIsEncrypt) {
      encrypt(file, content, encryptType, work.id)
    } else {
      decrypt(file, content, work.id)
    }
  })
  if (process.send) {
    process.send(work.id)
  }
}
